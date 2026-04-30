import type { Metadata } from "next";
import { Inter, Space_Mono } from "next/font/google";
import "./globals.css";
import SmoothScroll from "@/components/SmoothScroll";
import CustomCursor from "@/components/CustomCursor";
import HamburgerMenu from "@/components/HamburgerMenu";

const inter = Inter({ subsets: ["latin"], variable: "--font-sans" });
const spaceMono = Space_Mono({ weight: ["400", "700"], subsets: ["latin"], variable: "--font-mono" });

export const metadata: Metadata = {
  title: "SignEdu 8-Bit | Real BISINDO AI",
  description: "The future of inclusive tech powered by AI and WebSockets.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className={`${inter.variable} ${spaceMono.variable}`}>
      <body>
        <SmoothScroll>
          <CustomCursor />
          <HamburgerMenu />
          {children}
        </SmoothScroll>
      </body>
    </html>
  );
}
