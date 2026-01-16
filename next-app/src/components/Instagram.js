'use client';

import { useState, useEffect } from 'react';

export default function Instagram() {
    const images = [
        '261187398_578241289935103_5528419618173590066_n..jpg',
        '261474676_1198066010683425_4546154628925998117_n..jpg',
        '261962781_598096558169662_1290372496156943896_n..jpg',
        '261934196_613442609780737_1191985690364385337_n..jpg',
        '261490504_307548151264999_1988355608404932533_n..jpg',
        '277145677_312188677677933_3101476042461013835_n..jpg',
        '278508451_725874875234738_2590093110711883942_n..jpg',
        '311338747_429316972469695_7967545692968914837_n..jpg'
    ];

    const [currentIndex, setCurrentIndex] = useState(0);
    const [fade, setFade] = useState(true);

    useEffect(() => {
        const interval = setInterval(() => {
            setFade(false);
            setTimeout(() => {
                setCurrentIndex((prev) => (prev + 1) % images.length);
                setFade(true);
            }, 1000);
        }, 5000);
        return () => clearInterval(interval);
    }, [images.length]);

    return (
        <section id="instagram-section" style={{
            background: 'linear-gradient(135deg, #1c1d26 0%, #24262e 100%)',
            padding: '80px 0',
            position: 'relative',
            overflow: 'hidden'
        }}>
            {/* Background Light Effects */}
            <div className="bg-light-1" />
            <div className="bg-light-2" />

            <div className="container" style={{ position: 'relative', zIndex: 1, maxWidth: '1200px' }}>

                {/* Section Header */}
                <div style={{ textAlign: 'center', marginBottom: '60px' }}>
                    <h2 className="section-title">
                        Nosso Instagram
                    </h2>
                    <div className="title-underline" />
                </div>

                {/* Instagram Profile Header */}
                <div className="profile-card">
                    <div style={{ display: 'flex', alignItems: 'center', gap: '20px' }}>
                        <div className="profile-image-container">
                            <div className="profile-image-inner">
                                <img
                                    src="/assets/css/images/logo-branco-crop.png"
                                    alt="Subrider Logo"
                                    style={{ width: '100%', height: '100%', objectFit: 'contain' }}
                                />
                            </div>
                        </div>
                        <div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                <h3 style={{ margin: 0, fontSize: '1.4rem', fontWeight: '700', color: '#fff' }}>subrider_br</h3>
                                <i className="fas fa-check-circle" style={{ color: '#3897f0', fontSize: '1rem' }}></i>
                            </div>
                            <p style={{ margin: 0, fontSize: '0.9rem', color: 'rgba(255,255,255,0.6)' }}>Especialistas em performance e precisão</p>
                        </div>
                    </div>

                    <div className="profile-stats">
                        <div className="stat-item">
                            <div className="stat-value">1.2k</div>
                            <div className="stat-label">Seguidores</div>
                        </div>
                        <div className="stat-item">
                            <div className="stat-value">450</div>
                            <div className="stat-label">Posts</div>
                        </div>
                        <a
                            href="https://www.instagram.com/subrider_br/"
                            target="_blank"
                            className="follow-btn"
                        >
                            <i className="fab fa-instagram" style={{ fontSize: '1.2rem' }}></i> Seguir
                        </a>
                    </div>
                </div>

                {/* Main Content Layout */}
                <div className="content-grid">
                    {/* Left Feature: Animated Hero */}
                    <div className="hero-feature">
                        <img
                            src={`/assets/css/images/insta pics/${images[currentIndex]}`}
                            alt="Instagram Spotlight"
                            className={`hero-img ${fade ? 'fade-in' : 'fade-out'}`}
                        />
                        <div className="hero-overlay">
                            <div className="tag">
                                <span className="tag-dot"></span>
                                <span className="tag-text">Destaque da semana</span>
                            </div>
                            <h2 className="hero-title">Excelência em cada detalhe técnico</h2>
                            <p className="hero-text">
                                A precisão que seu motor merece, registrada em cada clique. Acompanhe nossos projetos exclusivos.
                            </p>
                        </div>
                    </div>

                    {/* Right Grid: Mini Items */}
                    <div className="mini-grid">
                        {images.slice(0, 4).map((img, i) => (
                            <a
                                key={i}
                                href="https://www.instagram.com/subrider_br/"
                                target="_blank"
                                className="grid-item"
                            >
                                <img
                                    src={`/assets/css/images/insta pics/${img}`}
                                    alt={`Instagram ${i}`}
                                    style={{
                                        width: '100%',
                                        height: '100%',
                                        objectFit: 'cover'
                                    }}
                                />
                                <div className="grid-overlay">
                                    <i className="fab fa-instagram" style={{ fontSize: '2.5rem', color: '#fff' }}></i>
                                    <span className="view-post">Ver Post</span>
                                </div>
                            </a>
                        ))}
                    </div>
                </div>

                {/* Bottom Gallery Showcase */}
                <div style={{ marginTop: '60px', overflow: 'hidden' }}>
                    <div className="infinite-scroll">
                        {[...images, ...images].map((img, i) => (
                            <div key={i} className="scroll-item">
                                <img
                                    src={`/assets/css/images/insta pics/${img}`}
                                    alt="Gallery"
                                    style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                                />
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Decorative Background Text */}
            <div className="bg-text">
                PRECISION PERFORMANCE PRECISION
            </div>

            <style jsx>{`
                .bg-light-1 {
                    position: absolute;
                    top: -10%;
                    right: -5%;
                    width: 600px;
                    height: 600px;
                    background: radial-gradient(circle, rgba(228, 76, 101, 0.12) 0%, transparent 70%);
                    filter: blur(80px);
                    z-index: 0;
                }
                .bg-light-2 {
                    position: absolute;
                    bottom: -10%;
                    left: -5%;
                    width: 600px;
                    height: 600px;
                    background: radial-gradient(circle, rgba(199, 54, 80, 0.08) 0%, transparent 70%);
                    filter: blur(80px);
                    z-index: 0;
                }
                .section-title {
                    font-size: clamp(2rem, 5vw, 3rem);
                    font-weight: 700;
                    margin-bottom: 15px;
                    background: linear-gradient(to right, #fff, rgba(255,255,255,0.5));
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                .title-underline {
                    height: 4px;
                    width: 60px;
                    background: #e44c65;
                    margin: 0 auto;
                    borderRadius: 2px;
                }
                .profile-card {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 40px;
                    padding: 24px 32px;
                    background: rgba(255, 255, 255, 0.03);
                    backdrop-filter: blur(12px);
                    border-radius: 30px;
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
                    flex-wrap: wrap;
                    gap: 20px;
                }
                .profile-image-container {
                    width: 70px;
                    height: 70px;
                    border-radius: 50%;
                    padding: 3px;
                    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
                }
                .profile-image-inner {
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    background: #1c1d26;
                    padding: 3px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    overflow: hidden;
                }
                .profile-stats {
                    display: flex;
                    gap: 30px;
                    align-items: center;
                }
                .stat-item {
                    text-align: center;
                }
                .stat-value {
                    font-weight: 700;
                    color: #fff;
                    font-size: 1.1rem;
                }
                .stat-label {
                    font-size: 0.75rem;
                    color: rgba(255,255,255,0.4);
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .follow-btn {
                    background: linear-gradient(45deg, #e44c65, #c73650);
                    border-radius: 15px;
                    padding: 12px 28px;
                    font-size: 0.9rem;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    color: #fff;
                    text-decoration: none;
                    font-weight: 600;
                    box-shadow: 0 10px 20px rgba(228, 76, 101, 0.3);
                    transition: all 0.3s ease;
                }
                .follow-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 15px 30px rgba(228, 76, 101, 0.4);
                    opacity: 1;
                }
                .content-grid {
                    display: flex;
                    gap: 24px;
                    height: 600px;
                }
                .hero-feature {
                    flex: 1.4;
                    position: relative;
                    border-radius: 32px;
                    overflow: hidden;
                    box-shadow: 0 30px 60px rgba(0,0,0,0.5);
                    border: 1px solid rgba(255,255,255,0.1);
                }
                .hero-img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: opacity 1s ease-in-out, transform 10s ease-out;
                }
                .hero-img.fade-in {
                    opacity: 1;
                    transform: scale(1.05);
                }
                .hero-img.fade-out {
                    opacity: 0;
                    transform: scale(1.15);
                }
                .hero-overlay {
                    position: absolute;
                    inset: 0;
                    padding: 50px;
                    background: linear-gradient(transparent, rgba(0,0,0,0.9));
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-end;
                }
                .tag {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 15px;
                    background: rgba(255,255,255,0.1);
                    backdrop-filter: blur(5px);
                    width: fit-content;
                    padding: 6px 16px;
                    borderRadius: 20px;
                    border: 1px solid rgba(255,255,255,0.1);
                }
                .tag-dot {
                    width: 8px;
                    height: 8px;
                    background: #e44c65;
                    border-radius: 50%;
                    box-shadow: 0 0 10px #e44c65;
                }
                .tag-text {
                    font-size: 0.8rem;
                    fontWeight: 600;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    color: #fff;
                }
                .hero-title {
                    font-size: clamp(1.8rem, 4vw, 2.8rem);
                    line-height: 1.2;
                    margin-bottom: 15px;
                    text-shadow: 0 2px 20px rgba(0,0,0,0.8);
                    color: #fff;
                }
                .hero-text {
                    font-size: 1.1rem;
                    color: rgba(255,255,255,0.7);
                    max-width: 85%;
                    line-height: 1.6;
                }
                .mini-grid {
                    flex: 1;
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    grid-template-rows: repeat(2, 1fr);
                    gap: 24px;
                }
                .grid-item {
                    position: relative;
                    border-radius: 24px;
                    overflow: hidden;
                    border: 1px solid rgba(255,255,255,0.1);
                    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                    display: block;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                }
                .grid-item:hover {
                    transform: translateY(-12px) scale(1.02);
                    box-shadow: 0 25px 50px rgba(0,0,0,0.4);
                }
                .grid-overlay {
                    position: absolute;
                    inset: 0;
                    background: linear-gradient(135deg, rgba(228, 76, 101, 0.6) 0%, rgba(199, 54, 80, 0.4) 100%);
                    backdrop-filter: blur(4px);
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transition: all 0.4s ease;
                    gap: 15px;
                }
                .grid-item:hover .grid-overlay {
                    opacity: 1;
                }
                .view-post {
                    color: #fff;
                    font-weight: 600;
                    font-size: 0.8rem;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                    border: 1px solid #fff;
                    padding: 4px 12px;
                    border-radius: 4px;
                }
                .infinite-scroll {
                    display: flex;
                    gap: 20px;
                    padding: 20px 0;
                    mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
                    animation: scroll 40s linear infinite;
                    width: max-content;
                }
                @keyframes scroll {
                    0% { transform: translateX(0); }
                    100% { transform: translateX(-50%); }
                }
                .scroll-item {
                    min-width: 180px;
                    height: 180px;
                    border-radius: 20px;
                    overflow: hidden;
                    flex-shrink: 0;
                    border: 1px solid rgba(255,255,255,0.05);
                    opacity: 0.4;
                    transition: all 0.3s ease;
                }
                .scroll-item:hover {
                    opacity: 1;
                    transform: scale(1.1);
                    z-index: 2;
                }
                .bg-text {
                    position: absolute;
                    top: 50%;
                    right: -10%;
                    transform: translateY(-50%) rotate(90deg);
                    color: rgba(255,255,255,0.02);
                    font-size: 10rem;
                    fontWeight: 900;
                    white-space: nowrap;
                    pointer-events: none;
                    user-select: none;
                }

                @media (max-width: 1024px) {
                    .content-grid {
                        flex-direction: column;
                        height: auto;
                    }
                    .hero-feature {
                        height: 500px;
                    }
                    .mini-grid {
                        grid-template-columns: repeat(2, 1fr);
                        height: 500px;
                    }
                }
                @media (max-width: 768px) {
                    .profile-card {
                        justify-content: center;
                        text-align: center;
                    }
                    .profile-stats {
                        width: 100%;
                        justify-content: center;
                    }
                    .hero-title {
                        font-size: 2rem;
                    }
                    .hero-overlay {
                        padding: 30px;
                    }
                }
                @media (max-width: 480px) {
                    .mini-grid {
                        grid-template-columns: 1fr;
                        height: auto;
                    }
                    .grid-item {
                        height: 250px;
                    }
                    .profile-stats {
                        gap: 15px;
                        flex-direction: column;
                    }
                    .follow-btn {
                        width: 100%;
                        justify-content: center;
                    }
                }
            `}</style>
        </section>
    );
}

