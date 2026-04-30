'use client'

import React, { useState, useRef, useLayoutEffect, useEffect } from 'react'
import gsap from 'gsap'
import Link from 'next/link'

export default function HamburgerMenu() {
  const [isOpen, setIsOpen] = useState(false)
  const menuRef = useRef<HTMLDivElement>(null)
  const tl = useRef<gsap.core.Timeline>(gsap.timeline({ paused: true }))

  useLayoutEffect(() => {
    let ctx = gsap.context(() => {
      tl.current = gsap.timeline({ paused: true })
        .to(menuRef.current, {
          clipPath: 'polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%)',
          duration: 0.8,
          ease: 'power4.inOut'
        })
        .from('.menu-link', {
          y: 50,
          opacity: 0,
          duration: 0.5,
          stagger: 0.1,
          ease: 'back.out(1.7)'
        }, '-=0.3')
    }, menuRef)
    return () => ctx.revert()
  }, [])

  useEffect(() => {
    if (isOpen) {
      tl.current.play()
    } else {
      tl.current.reverse()
    }
  }, [isOpen])

  return (
    <>
      <button 
        onClick={() => setIsOpen(!isOpen)}
        className="fixed top-6 right-6 z-[999] text-white hover:text-[#00ff88] transition-colors mix-blend-difference"
      >
        <div className="flex flex-col gap-1.5 items-end">
           <div className={`w-8 h-[2px] bg-current transition-all duration-300 ${isOpen ? 'rotate-45 translate-y-[8px]' : ''}`}></div>
           <div className={`w-6 h-[2px] bg-current transition-all duration-300 ${isOpen ? 'opacity-0' : ''}`}></div>
           <div className={`w-8 h-[2px] bg-current transition-all duration-300 ${isOpen ? '-rotate-45 -translate-y-[8px]' : ''}`}></div>
        </div>
      </button>

      <div 
        ref={menuRef} 
        className="fixed inset-0 bg-[#0f0f11] z-[998] flex flex-col justify-center items-center"
        style={{ clipPath: 'polygon(0% 0%, 100% 0%, 100% 0%, 0% 0%)' }}
      >
         <nav className="flex flex-col gap-8 text-center">
            <Link href="/" onClick={() => setIsOpen(false)} className="menu-link title-8bit text-5xl font-bold text-white hover:text-[#00ff88]">Home</Link>
            <Link href="/dashboard" onClick={() => setIsOpen(false)} className="menu-link title-8bit text-5xl font-bold text-white hover:text-[#00ff88]">Dashboard</Link>
            <Link href="#" className="menu-link title-8bit text-5xl font-bold text-gray-600 hover:text-white line-through">Leaderboard (TBA)</Link>
         </nav>
      </div>
    </>
  )
}
