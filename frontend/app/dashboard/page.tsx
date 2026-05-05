'use client'

import React, { useEffect, useRef, useState } from 'react'
import Link from 'next/link'
import { ArrowLeft, User, Zap, Shield } from 'lucide-react'
import confetti from 'canvas-confetti'

export default function Dashboard() {
  const videoRef = useRef<HTMLVideoElement>(null)
  const canvasRef = useRef<HTMLCanvasElement>(null)
  const wsRef = useRef<WebSocket | null>(null)

  const [activeSign, setActiveSign] = useState<string>("Halo")
  const [prediction, setPrediction] = useState<string | null>(null)
  const [userData, setUserData] = useState<any>({ xp: 0, level: 1 })
  const [completedSigns, setCompletedSigns] = useState<string[]>([])
  
  const signsList = ["Halo", "Terima_Kasih", "Ibu", "Ayah", "Makan", "Minum", "Tolong", "Saya", "Kamu", "Selamat"]

  const lastSpoken = useRef<number>(0)

  // Fetch Progress from Laravel API
  const fetchProgress = async () => {
    try {
      const res = await fetch("http://127.0.0.1:8000/api/progress")
      const data = await res.json()
      setUserData(data.user)
      setCompletedSigns(data.completed_signs)
    } catch (e) {
      console.log("No Laravel backend reachable.")
    }
  }

  useEffect(() => {
    fetchProgress()

    // 1. Setup Camera
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
      navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
          if (videoRef.current) {
            videoRef.current.srcObject = stream
          }
        })
    }

    // 2. Setup WebSocket
    wsRef.current = new WebSocket("ws://127.0.0.1:8001/ws")
    
    wsRef.current.onmessage = (event) => {
      const data = JSON.parse(event.data)
      drawLandmarks(data.landmarks)
      
      if (data.prediction && data.prediction === activeSign) {
        setPrediction(data.prediction)
        handleSuccess(data.prediction)
      } else {
        setPrediction(null)
      }
    }

    // 3. Send frames to Python via WebSocket
    const intervalId = setInterval(() => {
      if (videoRef.current && wsRef.current?.readyState === WebSocket.OPEN) {
        const canvas = document.createElement("canvas")
        canvas.width = 640
        canvas.height = 480
        const ctx = canvas.getContext("2d")
        if (ctx) {
          ctx.drawImage(videoRef.current, 0, 0, canvas.width, canvas.height)
          const base64Img = canvas.toDataURL("image/jpeg", 0.5)
          wsRef.current.send(base64Img)
        }
      }
    }, 100) // 10fps optimal for MVP over websockets

    return () => {
      clearInterval(intervalId)
      wsRef.current?.close()
    }
  }, [activeSign])

  const drawLandmarks = (landmarksList: any[]) => {
    const canvas = canvasRef.current
    if (!canvas) return
    const ctx = canvas.getContext("2d")
    if (!ctx) return

    ctx.clearRect(0, 0, canvas.width, canvas.height)

    // 8-bit styling drawing for points
    ctx.fillStyle = "#00ff88"
    ctx.strokeStyle = "#00ff88"
    ctx.lineWidth = 2

    landmarksList.forEach(hand => {
      hand.forEach((lm: any) => {
        const x = lm.x * canvas.width
        const y = lm.y * canvas.height
        // Draw square pixel instead of circle
        ctx.fillRect(x - 3, y - 3, 6, 6)
      })
    })
  }

  const handleSuccess = async (signText: string) => {
    const now = Date.now()
    if (now - lastSpoken.current < 3000) return // Debounce speech and API for 3 seconds
    lastSpoken.current = now

    // TTS Output (Indonesian Female)
    const utterance = new SpeechSynthesisUtterance(signText.replace("_", " "))
    utterance.lang = "id-ID"
    utterance.pitch = 1.2
    utterance.rate = 1.0
    window.speechSynthesis.speak(utterance)

    // Confetti
    confetti({
      particleCount: 100,
      spread: 70,
      origin: { y: 0.6 },
      colors: ['#00ff88', '#ffffff']
    })

    // Update Laravel XP
    try {
      const res = await fetch("http://127.0.0.1:8000/api/update-xp", {
        method: "POST",
        headers: { "Content-Type": "application/json", "Accept": "application/json" },
        body: JSON.stringify({ xp_earned: 50, sign_id: signText })
      })
      const apiData = await res.json()
      if (apiData.success) {
        setUserData({ ...userData, xp: apiData.xp, level: apiData.level })
        setCompletedSigns([...completedSigns, signText])
      }
    } catch(e) {
      console.log("Failed saving XP.")
    }
  }

  const handleAdminRedirect = () => {
    window.location.href = "http://127.0.0.1:8000/admin/login"
  }

  return (
    <div className="min-h-screen bg-black text-white p-6 font-mono selection:bg-[#00ff88] selection:text-black">
      {/* Header */}
      <header className="flex justify-between items-center mb-8 border-b-2 border-gray-800 pb-4">
        <div className="flex items-center gap-6">
          <Link href="/" className="hover:text-[#00ff88] transition-colors"><ArrowLeft size={24} /></Link>
          <div className="text-2xl font-bold tracking-widest text-[#00ff88]">SignEdu.ai</div>
        </div>
        
        <div className="flex items-center gap-6 pr-4">
          <button onClick={handleAdminRedirect} className="flex items-center gap-2 text-xs px-4 py-2 border border-[#00ff88] text-[#00ff88] hover:bg-[#00ff88] hover:text-black transition-colors font-bold uppercase tracking-wider">
            <Shield size={16} /> Admin Panel
          </button>
          <div className="flex items-center gap-2 text-[#00ff88]">
            <Zap size={20} className="fill-[#00ff88] animate-pulse" />
            <span className="font-bold text-xl">{userData.xp} XP</span>
          </div>
          <div className="px-3 py-1 border border-gray-600 rounded-sm">
            LVL {userData.level}
          </div>
          <User className="text-gray-400" />
        </div>
      </header>

      <div className="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {/* Left Col: Target Sign */}
        <div className="col-span-1 border-2 border-gray-800 p-6 relative bg-[#0f0f11]">
           <h2 className="text-gray-400 uppercase text-sm mb-4">Current Module</h2>
           <div className="text-5xl font-black text-white mb-8 bg-black border-l-4 border-[#00ff88] p-4 text-center">
             {activeSign.replace("_", " ")}
           </div>

           <p className="text-sm text-gray-500 mb-6">Position your hands in the frame to match the BISINDO sign. The AI will constantly detect and validate your gesture.</p>

           <div className="border border-gray-800 p-4">
              <h3 className="text-[#00ff88] mb-4 text-sm font-bold">MVP Vocabulary</h3>
              <div className="flex flex-wrap gap-2">
                 {signsList.map(sign => (
                   <button 
                      key={sign}
                      onClick={() => setActiveSign(sign)}
                      className={`text-xs px-2 py-1 border ${activeSign === sign ? 'border-[#00ff88] bg-[#00ff88] text-black' : 'border-gray-700 text-gray-400 hover:border-gray-500'} ${completedSigns.includes(sign) ? 'opacity-50' : ''}`}
                    >
                     {sign.replace("_", " ")} {completedSigns.includes(sign) && '✓'}
                   </button>
                 ))}
              </div>
           </div>
        </div>

        {/* Right Col: Video Feed */}
        <div className="col-span-1 lg:col-span-2 border-2 border-gray-800 p-6 relative bg-[#050505] flex flex-col justify-center items-center overflow-hidden">
            
            <div className="relative w-full max-w-[640px] aspect-video border-2 border-dashed border-gray-700">
               
               {/* 1. Underlying Camera Feed */}
               <video 
                  ref={videoRef} 
                  autoPlay 
                  playsInline 
                  muted 
                  className="absolute inset-0 w-full h-full object-cover origin-center -scale-x-100 opacity-60"
               />
               
               {/* 2. Top-Layer Dynamic ML Overlay */}
               <canvas 
                  ref={canvasRef} 
                  width={640} 
                  height={480}
                  className="absolute inset-0 w-full h-full pointer-events-none -scale-x-100 mix-blend-screen"
               />

               {/* Indicator overlay */}
               {prediction === activeSign && (
                 <div className="absolute top-4 right-4 bg-[#00ff88] text-black font-bold px-4 py-2 animate-bounce border-2 border-white">
                   {prediction} MATCH!
                 </div>
               )}
            </div>

            <div className="mt-6 flex justify-between w-full max-w-[640px] text-xs text-gray-500">
              <span>● ML Inferencing ACTIVE</span>
              <span>10 FPS Cap</span>
            </div>
        </div>

      </div>

    </div>
  )
}
