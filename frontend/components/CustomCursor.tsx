'use client'

import React, { useEffect, useRef } from 'react'
import gsap from 'gsap'

export default function CustomCursor() {
  const cursorRef = useRef<HTMLDivElement>(null)
  const followerRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const ctx = gsap.context(() => {
      window.addEventListener('mousemove', (e) => {
        // Main dot instant follow
        gsap.to(cursorRef.current, {
          x: e.clientX,
          y: e.clientY,
          duration: 0,
        })

        // Follower trailing effect
        gsap.to(followerRef.current, {
          x: e.clientX - 12,
          y: e.clientY - 12,
          duration: 0.3,
          ease: 'power2.out',
        })
      })

      const interactables = document.querySelectorAll('a, button, input')
      interactables.forEach((el) => {
        el.addEventListener('mouseenter', () => {
          gsap.to(followerRef.current, { scale: 1.5, borderColor: '#00ff88', duration: 0.3 })
          gsap.to(cursorRef.current, { opacity: 0, duration: 0.2 })
        })
        el.addEventListener('mouseleave', () => {
          gsap.to(followerRef.current, { scale: 1, borderColor: '#e0e0e0', duration: 0.3 })
          gsap.to(cursorRef.current, { opacity: 1, duration: 0.2 })
        })
      })
    })

    return () => ctx.revert()
  }, [])

  return (
    <>
      <div 
        ref={cursorRef} 
        className="fixed top-0 left-0 w-2 h-2 bg-white rounded-none pointer-events-none z-[9999] mix-blend-difference"
      />
      <div 
        ref={followerRef} 
        className="fixed top-0 left-0 w-8 h-8 border-2 border-white rounded-none pointer-events-none z-[9998] transition-colors duration-200"
        style={{ clipPath: 'polygon(0 10%, 10% 10%, 10% 0, 90% 0, 90% 10%, 100% 10%, 100% 90%, 90% 90%, 90% 100%, 10% 100%, 10% 90%, 0 90%)' }} // 8-bit styling
      />
    </>
  )
}
