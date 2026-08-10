import * as THREE from 'three';

const VERTEX_SHADER = `
    uniform float uTime;
    uniform vec2 uPointer;
    attribute float aScale;
    varying float vGlow;

    void main() {
        vec3 transformed = position;
        transformed.y += sin(uTime * 0.22 + position.x * 0.8) * 0.08;
        transformed.x += uPointer.x * (0.08 + aScale * 0.04);
        transformed.y += uPointer.y * (0.05 + aScale * 0.03);

        vec4 viewPosition = modelViewMatrix * vec4(transformed, 1.0);
        gl_Position = projectionMatrix * viewPosition;
        gl_PointSize = aScale * (42.0 / -viewPosition.z);
        vGlow = aScale;
    }
`;

const FRAGMENT_SHADER = `
    varying float vGlow;

    void main() {
        float distanceFromCenter = distance(gl_PointCoord, vec2(0.5));
        float alpha = smoothstep(0.5, 0.04, distanceFromCenter) * (0.14 + vGlow * 0.08);
        vec3 color = mix(vec3(0.92, 0.48, 0.62), vec3(1.0, 0.88, 0.66), vGlow);
        gl_FragColor = vec4(color, alpha);
    }
`;

export class ArticleAtmosphere {
    constructor(canvas) {
        this.canvas = canvas;
        this.frame = null;
        this.lastTime = 0;
        this.isVisible = !document.hidden;
        this.resizeFrame = null;
        this.pointerTarget = new THREE.Vector2();
    }

    init() {
        if (!this.canvas) return;

        this.renderer = new THREE.WebGLRenderer({ canvas: this.canvas, alpha: true, antialias: false, powerPreference: 'low-power' });
        this.renderer.setClearColor(0x000000, 0);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));

        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(42, 1, .1, 30);
        this.camera.position.z = 4.5;

        const particleCount = window.innerWidth < 1100 ? 48 : 78;
        const positions = new Float32Array(particleCount * 3);
        const scales = new Float32Array(particleCount);

        for (let index = 0; index < particleCount; index += 1) {
            positions[index * 3] = (Math.random() - .5) * 7;
            positions[index * 3 + 1] = (Math.random() - .5) * 5;
            positions[index * 3 + 2] = (Math.random() - .5) * 2;
            scales[index] = .45 + Math.random() * 1.15;
        }

        this.geometry = new THREE.BufferGeometry();
        this.geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        this.geometry.setAttribute('aScale', new THREE.BufferAttribute(scales, 1));

        this.material = new THREE.ShaderMaterial({
            uniforms: {
                uTime: { value: 0 },
                uPointer: { value: new THREE.Vector2() },
            },
            vertexShader: VERTEX_SHADER,
            fragmentShader: FRAGMENT_SHADER,
            transparent: true,
            depthWrite: false,
            blending: THREE.AdditiveBlending,
        });

        this.particles = new THREE.Points(this.geometry, this.material);
        this.scene.add(this.particles);

        this.onResize = () => {
            if (this.resizeFrame !== null) return;
            this.resizeFrame = requestAnimationFrame(() => {
                this.resizeFrame = null;
                this.resize();
            });
        };
        this.onPointerMove = (event) => {
            this.pointerTarget.set(
                (event.clientX / window.innerWidth - .5) * 2,
                -(event.clientY / window.innerHeight - .5) * 2,
            );
        };
        this.onVisibilityChange = () => {
            this.isVisible = !document.hidden;
            if (this.isVisible && this.frame === null) this.animate(performance.now());
        };

        window.addEventListener('resize', this.onResize, { passive: true });
        window.addEventListener('pointermove', this.onPointerMove, { passive: true });
        document.addEventListener('visibilitychange', this.onVisibilityChange);
        this.resize();
        this.animate(performance.now());
    }

    resize() {
        const width = window.innerWidth;
        const height = window.innerHeight;
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height, false);
    }

    animate(time) {
        this.frame = null;
        if (!this.isVisible || !this.renderer) return;

        const delta = Math.min((time - this.lastTime) / 1000, .05);
        this.lastTime = time;
        this.material.uniforms.uTime.value += delta;
        this.material.uniforms.uPointer.value.lerp(this.pointerTarget, .035);
        this.particles.rotation.z += delta * .006;
        this.renderer.render(this.scene, this.camera);
        this.frame = requestAnimationFrame((nextTime) => this.animate(nextTime));
    }

    destroy() {
        if (this.frame !== null) cancelAnimationFrame(this.frame);
        if (this.resizeFrame !== null) cancelAnimationFrame(this.resizeFrame);
        window.removeEventListener('resize', this.onResize);
        window.removeEventListener('pointermove', this.onPointerMove);
        document.removeEventListener('visibilitychange', this.onVisibilityChange);
        this.geometry?.dispose();
        this.material?.dispose();
        this.renderer?.dispose();
        this.renderer?.forceContextLoss();
    }
}
