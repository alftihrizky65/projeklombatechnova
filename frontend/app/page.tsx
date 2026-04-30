'use client'

import React, { useLayoutEffect, useRef } from 'react'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Link from 'next/link'

gsap.registerPlugin(ScrollTrigger)

export default function Home() {
  const container = useRef<HTMLDivElement>(null)
  
  useLayoutEffect(() => {
    const ctx = gsap.context(() => {
      // Pin Hero Section
      ScrollTrigger.create({
        trigger: ".hero-section",
        start: "top top",
        end: "+=1000",
        pin: true,
        pinSpacing: true,
      });

      gsap.to(".hero-overlay", {
        scrollTrigger: {
          trigger: ".hero-section",
          start: "top top",
          end: "+=1000",
          scrub: true,
        },
        opacity: 0,
      });

      // Split Text Effect Simulation
      const texts = gsap.utils.toArray('.stagger-text');
      texts.forEach((text: any) => {
        gsap.fromTo(text, 
          { y: 100, opacity: 0 },
          { 
            y: 0, opacity: 1, duration: 1, ease: 'power4.out',
            scrollTrigger: {
              trigger: text,
              start: "top 80%",
            }
          }
        )
      })

    }, container)
    return () => ctx.revert()
  }, [])

  return (
    <div ref={container} className="relative min-h-[200vh]">
      
      {/* Navbar Minimalist */}
      <nav className="fixed w-full z-50 p-6 flex justify-between items-center mix-blend-difference">
        <div className="title-8bit text-2xl font-bold tracking-widest text-[#00ff88]">SignEdu</div>
        <Link href="/dashboard" className="title-8bit text-sm px-6 py-2 border-2 border-white hover:bg-white hover:text-black transition-colors">Start Learning</Link>
      </nav>

      {/* Hero Pinned Section */}
      <section className="hero-section h-screen w-full flex flex-col justify-center items-center relative overflow-hidden bg-[#0f0f11]">
        
        {/* Parallax 8-bit Background Element */}
        <div className="absolute inset-0 opacity-20 pointer-events-none" style={{
          backgroundImage: 'linear-gradient(#00ff88 1px, transparent 1px), linear-gradient(90deg, #00ff88 1px, transparent 1px)',
          backgroundSize: '50px 50px'
        }}></div>

        <div className="hero-overlay relative z-10 text-center max-w-4xl px-4">
          <h1 className="text-6xl md:text-8xl font-black mb-6 uppercase tracking-tighter leading-none">
            <span className="block stagger-text text-white">The Future Of</span>
            <span className="block inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#00ff88] to-emerald-400 stagger-text">Inclusive Tech.</span>
          </h1>
          <p className="text-xl md:text-2xl text-gray-400 mt-8 mb-12 stagger-text">
            Powered by real-time Computer Vision and <span className="text-white font-bold">Bahasa Isyarat Indonesia (BISINDO)</span>
          </p>
        </div>

      </section>

      {/* Storytelling Section */}
      <section className="min-h-screen bg-black flex items-center justify-center relative py-20 px-8">
        <div className="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
          <div>
            <h2 className="title-8bit text-4xl text-[#00ff88] mb-6 stagger-text">Why It Matters</h2>
            <p className="text-gray-300 text-lg leading-relaxed mb-6 stagger-text">
              Millions of individuals rely on sign language as their primary mode of communication. Traditional barriers have prevented real interaction. 
            </p>
            <p className="text-gray-300 text-lg leading-relaxed stagger-text">
              SignEdu bridges this gap through a cutting-edge AI engine using Random Forest classification and MediaPipe to detect BISINDO landmarks instantly via your webcam.
            </p>
          </div>
          <div className="glass p-12 relative overflow-hidden group">
             <div className="absolute inset-0 bg-[#00ff88] opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
             <h3 className="text-2xl font-bold text-white mb-4 stagger-text">Production Ready MVP</h3>
             <ul className="space-y-4 text-gray-400">
               <li className="flex items-center gap-3 stagger-text"><span className="w-2 h-2 bg-[#00ff88]"></span> Scikit-Learn Fast Inference</li>
               <li className="flex items-center gap-3 stagger-text"><span className="w-2 h-2 bg-[#00ff88]"></span> 60fps WebSocket Streaming</li>
               <li className="flex items-center gap-3 stagger-text"><span className="w-2 h-2 bg-[#00ff88]"></span> Laravel Gamification API</li>
             </ul>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="h-screen flex flex-col justify-center items-center bg-[#00ff88] text-black">
        <h2 className="text-6xl md:text-8xl font-black mb-8 uppercase text-center stagger-text">Ready to <br/>Sign?</h2>
        <Link href="/dashboard" className="title-8bit text-xl font-bold px-12 py-6 border-4 border-black bg-black text-[#00ff88] hover:bg-[#00cc6a] hover:text-black hover:border-black transition-all">
          Enter Dashboard
        </Link>
      </section>

    </div>
  )
}
