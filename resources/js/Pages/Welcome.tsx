import React, { useRef } from 'react';
import { Canvas, useFrame } from '@react-three/fiber';
import { OrbitControls, PerspectiveCamera, useGLTF } from '@react-three/drei';
import { Group, PointLight, SpotLight, Color } from 'three';
import { motion } from 'framer-motion';
import { Link, Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import { Button } from "@/shadcn/ui/button";

function PoliceCar() {
    const group = useRef<Group | null>(null);
    const redLight = useRef<PointLight>(null);
    const blueLight = useRef<PointLight>(null);
    const redSpotLight = useRef<SpotLight>(null);
    const blueSpotLight = useRef<SpotLight>(null);
    const { scene: carScene } = useGLTF('/models/police_car.glb') as any;

    useFrame((state, delta) => {
        if (group.current) {
            group.current.rotation.y += delta * 0.05;
        }

        const time = state.clock.getElapsedTime();
        const flashIntensity = Math.sin(time * 10) * 0.5 + 0.5;

        if (redLight.current) {
            redLight.current.intensity = flashIntensity * 5;
            redLight.current.color = new Color('red');
        }

        if (blueLight.current) {
            blueLight.current.intensity = (1 - flashIntensity) * 5;
            blueLight.current.color = new Color('blue');
        }

        if (redSpotLight.current) {
            redSpotLight.current.intensity = flashIntensity * 5;
        }

        if (blueSpotLight.current) {
            blueSpotLight.current.intensity = (1 - flashIntensity) * 5;
        }
    });

    return (
        <group ref={group} position={[0, 0, 0]}>
            <primitive object={carScene} scale={1.5} position={[0, -0.3, 0]} />
            <pointLight ref={redLight} position={[-2, 3, 0]} intensity={2} color="red" distance={15} decay={2} />
            <pointLight ref={blueLight} position={[2, 3, 0]} intensity={2} color="blue" distance={15} decay={2} />
            <spotLight ref={redSpotLight} position={[-2, 3, 0]} angle={0.3} penumbra={1} color="red" intensity={2} distance={15} />
            <spotLight ref={blueSpotLight} position={[2, 3, 0]} angle={0.3} penumbra={1} color="blue" intensity={2} distance={15} />
        </group>
    );
}

function ReflectiveSurface() {
    return (
        <mesh rotation={[-Math.PI / 2, 0, 0]} position={[0, -0.5, 0]}>
            <planeGeometry args={[50, 50]} />
            <meshStandardMaterial color="darkgray" metalness={0.8} roughness={0.2} />
        </mesh>
    );
}

function BackgroundLights() {
    const lightColor = new Color(0x404040);
    return (
        <>
            <spotLight position={[-10, 10, 10]} angle={0.3} penumbra={0.5} intensity={2} color={lightColor} />
            <spotLight position={[10, 10, 10]} angle={0.3} penumbra={0.5} intensity={2} color={lightColor} />
            <spotLight position={[-10, 10, -10]} angle={0.3} penumbra={0.5} intensity={2} color={lightColor} />
            <spotLight position={[10, 10, -10]} angle={0.3} penumbra={0.5} intensity={2} color={lightColor} />
        </>
    );
}

export default function Welcome({ auth, laravelVersion, phpVersion }: PageProps<{ laravelVersion: string, phpVersion: string }>) {
    return (
        <>
            <Head title="Welcome to JusticeRP" />
            <div className="min-h-screen bg-gradient-to-b from-gray-900 to-black text-white font-sans relative overflow-hidden">
                <header className="relative z-10 bg-black p-6 shadow-md border-b border-gray-700">
                    <nav className="container mx-auto flex justify-between items-center border border-gray-700 rounded-lg p-4">
                        <motion.div
                            className="flex items-center space-x-4"
                            initial={{ y: -250 }}
                            animate={{ y: 0 }}
                            transition={{ type: "spring", stiffness: 120 }}
                        >
                            <img src="https://utfs.io/f/dbe186b0-89bf-4548-8b8b-3bbca8d77c0e-29ew.png" alt="JusticeRP Logo" style={{ width: '60px', height: 'auto' }} className="h-10 w-10" />
                            <h1 className="text-4xl font-bold text-white">JusticeRP</h1>
                        </motion.div>
                        <ul className="flex space-x-4 text-lg">
                            <li><Link href="#about" className="text-gray-300 hover:text-white">About</Link></li>
                            <li><Link href="#join" className="text-gray-300 hover:text-white">Join</Link></li>
                            <li><Link href="#info" className="text-gray-300 hover:text-white">Server Info</Link></li>
                        </ul>
                    </nav>
                </header>
                <main className="relative z-10 container mx-auto py-16">
                    <section className="text-center mb-16 relative z-10">
                        <Canvas className="relative z-0">
                            <PerspectiveCamera makeDefault position={[0, 2, 8]} />
                            <ambientLight intensity={0.5} />
                            <BackgroundLights />
                            <OrbitControls enableZoom={false} />
                            <ReflectiveSurface />
                            <PoliceCar />
                        </Canvas>
                        <motion.h2
                            className="text-3xl font-bold mt-8 text-white relative z-10"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ delay: 1 }}
                        >
                            Experience High-Speed Police Chases
                        </motion.h2>
                        <motion.p
                            className="text-lg text-gray-400 relative z-10"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ delay: 1.5 }}
                        >
                            Join our community for an immersive role-playing experience with custom in-house development, large community events, and more.
                        </motion.p>
                    </section>
                    <section id="about" className="mt-8 relative z-10">
                        <motion.h2
                            className="text-3xl font-bold mb-8 text-white"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ delay: 1 }}
                        >
                            What is JusticeRP?
                        </motion.h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <motion.div
                                className="bg-gray-800 p-8 rounded shadow-lg border border-gray-700"
                                whileHover={{ scale: 1.05 }}
                            >
                                <h3 className="text-xl font-bold text-white">About JusticeRP</h3>
                                <p className="mt-4 text-gray-400">JusticeRP is a premier FiveM server known for its large, active community and exceptional in-house development. We offer a realistic role-playing environment with a wide range of activities, including law enforcement, civilian life, and emergency services.</p>
                            </motion.div>
                            <motion.div
                                className="bg-gray-800 p-8 rounded shadow-lg border border-gray-700"
                                whileHover={{ scale: 1.05 }}
                            >
                                <h3 className="text-xl font-bold text-white">What is FiveM?</h3>
                                <p className="mt-4 text-gray-400">FiveM is a modification framework for GTA V, allowing you to play on customized multiplayer servers. Experience a variety of game modes, including role-playing, racing, and more.</p>
                            </motion.div>
                            <motion.div
                                className="bg-gray-800 p-8 rounded shadow-lg border border-gray-700"
                                whileHover={{ scale: 1.05 }}
                            >
                                <h3 className="text-xl font-bold text-white">Store</h3>
                                <p className="mt-4 text-gray-400">Visit our store to purchase car packs, VIP memberships, and other exclusive items to enhance your experience on JusticeRP.</p>
                                <Button className="mt-4">Visit Store</Button>
                            </motion.div>
                        </div>
                    </section>
                </main>
                <footer className="relative z-10 bg-black p-4 text-center border-t border-gray-700">
                    <p className="text-gray-500">© 2024 JusticeRP. All rights reserved.</p>
                </footer>
            </div>
        </>
    );
}
