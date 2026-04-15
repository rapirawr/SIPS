import * as THREE from 'three';
import { FBXLoader } from 'three/examples/jsm/loaders/FBXLoader.js';
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';

/**
 * SIPS Three.js Hero Scene
 * ─────────────────────────
 * Karakter 3D dengan dual-animation:
 * - Idle (loop) + Waving (otomatis setiap 4.5 detik)
 * - Blending manual via weight lerp (anti T-pose)
 */

class HeroScene {
    constructor() {
        this.container = document.getElementById('three-hero-canvas');
        if (!this.container) return;

        this.mouse = { x: 0, y: 0 };
        this.targetMouse = { x: 0, y: 0 };

        // FIX #1: Gunakan dua clock terpisah agar getDelta() tidak mengganggu
        // pembacaan elapsedTime untuk animasi posisi (sin wave, dll.)
        this.clock = new THREE.Clock();         // untuk delta (mixer + lerp)
        this.elapsedClock = new THREE.Clock();  // untuk elapsedTime (sin wave)

        // === Animation blending state ===
        this.mixer = null;
        this.idleAction = null;
        this.waveAction = null;
        this.actionAction = null; // Animasi action.fbx
        this.blendTarget = 0;
        this.blendCurrent = 0;
        this.blendMode = 'idle'; // 'idle' | 'wave' | 'action'

        // Counter: setelah 2x waving, mainkan action
        this.waveCount = 0;

        // FIX #2: Konstanta untuk exponential decay (frame-rate independent)
        // Nilai 0.05 = "mencapai 95% target dalam ~1 detik" (tau ~= -1/ln(0.05) ≈ 0.33s per step)
        // Rumus: weight = 1 - Math.exp(-blendSpeed * delta)
        this.blendSpeed = 5;

        // FIX #3: Simpan referensi timer agar bisa di-cleanup
        this._waveInterval = null;
        this._waveTimeout = null;
        this._resizeTimeout = null;

        this._animFrameId = null;

        this.init();
        this.loadCharacter();
        this.addEventListeners();
        this.animate();
    }

    init() {
        this.scene = new THREE.Scene();

        const aspect = this.container.clientWidth / this.container.clientHeight;
        this.camera = new THREE.PerspectiveCamera(50, aspect, 0.1, 1000);
        this.camera.position.z = 6;

        this.renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true,
            powerPreference: 'high-performance'
        });
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.setClearColor(0x000000, 0);
        this.renderer.outputColorSpace = THREE.SRGBColorSpace;
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.0;

        this.container.appendChild(this.renderer.domElement);

        // Environment Map
        const pmremGenerator = new THREE.PMREMGenerator(this.renderer);
        this.scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;
        pmremGenerator.dispose(); // FIX #4: Dispose generator setelah dipakai

        // Lights
        this.scene.add(new THREE.AmbientLight(0xffffff, 0.4));

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.5);
        dirLight.position.set(5, 5, 5);
        this.scene.add(dirLight);

        const pinkLight = new THREE.PointLight(0xcc2c6b, 0.8, 15);
        pinkLight.position.set(-3, 2, 3);
        this.scene.add(pinkLight);

        const blueLight = new THREE.PointLight(0x6366f1, 0.6, 12);
        blueLight.position.set(3, -2, 2);
        this.scene.add(blueLight);

        const warmLight = new THREE.PointLight(0xf59e0b, 0.4, 10);
        warmLight.position.set(0, 3, -2);
        this.scene.add(warmLight);
    }

    loadCharacter() {
        const loader = new FBXLoader();
        this.mainMesh = new THREE.Group();
        this.scene.add(this.mainMesh);

        loader.load(
            '/storage/asset/3d/iddle.fbx',
            (model) => {
                // Auto-Scale & Auto-Center
                const box = new THREE.Box3().setFromObject(model);
                const size = box.getSize(new THREE.Vector3());
                const center = box.getCenter(new THREE.Vector3());
                const targetSize = 3;
                const maxDim = Math.max(size.x, size.y, size.z);

                // FIX #5: Guard division-by-zero jika model kosong
                if (maxDim === 0) {
                    console.warn('[SIPS 3D] Model bounding box nol, skip scaling.');
                    return;
                }

                const scaleFactor = targetSize / maxDim;
                model.scale.setScalar(scaleFactor);
                model.position.set(
                    -center.x * scaleFactor,
                    -center.y * scaleFactor - 0.8, // Geser model ke bawah
                    -center.z * scaleFactor
                );

                model.traverse((child) => {
                    if (child.isMesh) {
                        // FIX #6: Handle array material & single material dengan aman
                        const materials = Array.isArray(child.material)
                            ? child.material
                            : [child.material];

                        materials.forEach((mat) => {
                            if (!mat) return;
                            mat.needsUpdate = true;
                            if (mat.map) {
                                mat.map.colorSpace = THREE.SRGBColorSpace;
                            }
                            mat.envMapIntensity = 1.5;
                        });

                        child.castShadow = true;
                        child.receiveShadow = true;
                    }
                });

                this.mainMesh.add(model);
                this.mixer = new THREE.AnimationMixer(model);

                // === IDLE: loop dengan bobot penuh di awal ===
                if (model.animations && model.animations.length > 0) {
                    this.idleAction = this.mixer.clipAction(model.animations[0]);
                    this.idleAction.setLoop(THREE.LoopRepeat);
                    this.idleAction.setEffectiveWeight(1);
                    this.idleAction.play();
                    console.log('[SIPS 3D] Idle animation aktif.');
                }

                // Muat waving animation
                loader.load(
                    '/storage/asset/3d/waving.fbx',
                    (wavingFBX) => {
                        if (!wavingFBX.animations || wavingFBX.animations.length === 0) {
                            console.warn('[SIPS 3D] waving.fbx tidak punya animasi.');
                            return;
                        }

                        const clip = wavingFBX.animations[0];
                        this.waveClipDuration = clip.duration;
                        this.waveAction = this.mixer.clipAction(clip);
                        this.waveAction.setLoop(THREE.LoopRepeat);
                        this.waveAction.setEffectiveWeight(0);
                        this.waveAction.play();

                        console.log('[SIPS 3D] Wave animation siap. Durasi: ' + clip.duration.toFixed(2) + 's');

                        // Muat action animation
                        loader.load(
                            '/storage/asset/3d/action.fbx',
                            (actionFBX) => {
                                if (!actionFBX.animations || actionFBX.animations.length === 0) {
                                    console.warn('[SIPS 3D] action.fbx tidak punya animasi.');
                                    // Tetap mulai loop meskipun action gagal
                                    this.startAutoWaveLoop();
                                    return;
                                }

                                const actionClip = actionFBX.animations[0];
                                this.actionClipDuration = actionClip.duration;
                                this.actionAction = this.mixer.clipAction(actionClip);
                                this.actionAction.setLoop(THREE.LoopRepeat);
                                this.actionAction.setEffectiveWeight(0);
                                this.actionAction.play();

                                console.log('[SIPS 3D] Action animation siap. Durasi: ' + actionClip.duration.toFixed(2) + 's');

                                // Semua animasi siap, mulai siklus!
                                this.startAutoWaveLoop();
                            },
                            undefined,
                            (error) => {
                                console.error('[SIPS 3D] Gagal load action.fbx:', error);
                                // Fallback: jalankan loop tanpa action
                                this.startAutoWaveLoop();
                            }
                        );
                    },
                    undefined,
                    (error) => {
                        console.error('[SIPS 3D] Gagal load waving.fbx:', error);
                    }
                );
            },
            (xhr) => {
                if (xhr.total > 0) {
                    console.log('[SIPS 3D] Loading: ' + Math.round((xhr.loaded / xhr.total) * 100) + '%');
                }
            },
            (error) => {
                console.error('[SIPS 3D] Gagal load iddle.fbx:', error);
            }
        );
    }

    /**
     * Siklus otomatis: setiap 4.5 detik, blend ke waving selama ~2.5 detik
     * lalu kembali ke idle.
     *
     * FIX #8: Simpan referensi interval & timeout agar bisa di-cleanup,
     * dan pastikan tidak ada interval ganda jika dipanggil ulang.
     */
    startAutoWaveLoop() {
        this.stopAutoWaveLoop();

        const waveDurationMs = (this.waveClipDuration || 2.5) * 1000;
        const actionDurationMs = (this.actionClipDuration || 3) * 1000;

        const runCycle = () => {
            this.waveCount++;

            if (this.waveCount >= 3 && this.actionAction) {
                // Setiap 2x waving → mainkan action, lalu reset counter
                this.waveCount = 0;
                this.triggerAction();

                // Setelah action selesai, jeda 2 detik lalu mulai siklus baru
                this._waveTimeout = setTimeout(() => {
                    this._waveTimeout = setTimeout(runCycle, 2000);
                }, actionDurationMs);
            } else {
                // Mainkan waving biasa
                this.triggerWave();

                // Setelah wave selesai, jeda 2 detik lalu siklus berikutnya
                this._waveTimeout = setTimeout(() => {
                    this._waveTimeout = setTimeout(runCycle, 2000);
                }, waveDurationMs);
            }
        };

        // Jeda 2 detik sebelum siklus pertama
        this._waveTimeout = setTimeout(runCycle, 2000);
    }

    stopAutoWaveLoop() {
        if (this._waveInterval !== null) {
            clearInterval(this._waveInterval);
            this._waveInterval = null;
        }
        if (this._waveTimeout !== null) {
            clearTimeout(this._waveTimeout);
            this._waveTimeout = null;
        }
    }

    triggerWave() {
        this.blendMode = 'wave';
        this.blendTarget = 1;

        const waveDurationMs = (this.waveClipDuration || 2.5) * 1000;
        setTimeout(() => {
            this.blendTarget = 0;
        }, waveDurationMs);
    }

    triggerAction() {
        this.blendMode = 'action';
        this.blendTarget = 1;

        const actionDurationMs = (this.actionClipDuration || 3) * 1000;
        setTimeout(() => {
            this.blendTarget = 0;
        }, actionDurationMs);
    }

    addEventListeners() {
        this._onMouseMove = (e) => {
            this.targetMouse.x = (e.clientX / window.innerWidth) * 2 - 1;
            this.targetMouse.y = -(e.clientY / window.innerHeight) * 2 + 1;
        };

        // FIX #10: Throttle resize dengan debounce 100ms
        this._onResize = () => {
            if (this._resizeTimeout) clearTimeout(this._resizeTimeout);
            this._resizeTimeout = setTimeout(() => {
                if (!this.container) return;
                const width = this.container.clientWidth;
                const height = this.container.clientHeight;
                this.camera.aspect = width / height;
                this.camera.updateProjectionMatrix();
                this.renderer.setSize(width, height);
            }, 100);
        };

        window.addEventListener('mousemove', this._onMouseMove);
        window.addEventListener('resize', this._onResize);
    }

    animate() {
        this._animFrameId = requestAnimationFrame(() => this.animate());

        // FIX #1 (impl): getDelta() dari clock utama untuk physics/mixer
        const delta = this.clock.getDelta();
        // getElapsedTime() dari clock terpisah untuk sin wave — tidak terpengaruh getDelta()
        const elapsed = this.elapsedClock.getElapsedTime();

        // Smooth mouse follow
        this.mouse.x += (this.targetMouse.x - this.mouse.x) * 0.05;
        this.mouse.y += (this.targetMouse.y - this.mouse.y) * 0.05;

        // =====================================================
        // WEIGHT BLENDING — Frame-Rate Independent
        // =====================================================
        // FIX #2 (impl): Exponential decay agar kecepatan blend konsisten
        // di 30fps maupun 120fps.
        // alpha = 1 - e^(-speed * delta)
        // Saat delta kecil (fps tinggi) → alpha kecil → perubahan halus per frame
        // Saat delta besar (fps rendah) → alpha besar → tetap mencapai target dalam waktu sama
        const alpha = 1 - Math.exp(-this.blendSpeed * delta);
        this.blendCurrent += (this.blendTarget - this.blendCurrent) * alpha;
        this.blendCurrent = Math.max(0, Math.min(1, this.blendCurrent));

        if (this.idleAction) {
            if (this.blendMode === 'wave' && this.waveAction) {
                // Idle ↔ Wave blending
                this.idleAction.setEffectiveWeight(1 - this.blendCurrent);
                this.waveAction.setEffectiveWeight(this.blendCurrent);
                if (this.actionAction) this.actionAction.setEffectiveWeight(0);
            } else if (this.blendMode === 'action' && this.actionAction) {
                // Idle ↔ Action blending
                this.idleAction.setEffectiveWeight(1 - this.blendCurrent);
                if (this.waveAction) this.waveAction.setEffectiveWeight(0);
                this.actionAction.setEffectiveWeight(this.blendCurrent);
            } else {
                // Full idle
                this.idleAction.setEffectiveWeight(1);
                if (this.waveAction) this.waveAction.setEffectiveWeight(0);
                if (this.actionAction) this.actionAction.setEffectiveWeight(0);
            }
        }

        // Update mixer
        if (this.mixer) {
            this.mixer.update(delta);
        }

        // Subtle mouse-reactive rotation
        if (this.mainMesh) {
            this.mainMesh.rotation.y = 0.1 + this.mouse.x * 0.08;
            this.mainMesh.rotation.x = this.mouse.y * 0.08;
            this.mainMesh.rotation.z = 0;
            // FIX #1 (impl): elapsed dari elapsedClock — akurat & tidak reset tiap frame
            this.mainMesh.position.y = Math.sin(elapsed * 0.8) * 0.15;
        }

        // Subtle camera movement
        this.camera.position.x = this.mouse.x * 0.5;
        this.camera.position.y = this.mouse.y * 0.3;
        this.camera.lookAt(0, 0, 0);

        this.renderer.render(this.scene, this.camera);
    }

    /**
     * Cleanup semua resource — panggil ini jika scene di-unmount
     * (misal: SPA navigation / component destroy)
     */
    dispose() {
        if (this._animFrameId !== null) {
            cancelAnimationFrame(this._animFrameId);
        }
        this.stopAutoWaveLoop();
        window.removeEventListener('mousemove', this._onMouseMove);
        window.removeEventListener('resize', this._onResize);
        if (this._resizeTimeout) clearTimeout(this._resizeTimeout);

        if (this.mixer) this.mixer.stopAllAction();
        this.renderer.dispose();
        this.scene.environment?.dispose();
    }
}

// Initialize when DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new HeroScene());
} else {
    new HeroScene();
}

export default HeroScene;