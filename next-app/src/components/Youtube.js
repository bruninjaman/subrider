'use client';

import { useState, useEffect } from 'react';

export default function Youtube() {
    const [videos, setVideos] = useState([]);
    const [hoveredId, setHoveredId] = useState(null);

    useEffect(() => {
        const fetchVideos = async () => {
            try {
                const res = await fetch('/api/youtube');
                const data = await res.json();

                if (Array.isArray(data) && data.length > 0) {
                    setVideos(data);
                } else {
                    // Fallback mock if API fails or is empty
                    setVideos([
                        { videoId: 'dQw4w9WgXcQ', title: 'Manutenção de Motor 4 Cilindros', description: 'Confira os detalhes da retífica completa deste motor esportivo.' },
                        { videoId: 'jNQXAC9IVRw', title: 'Estética Automotiva em Motos', description: 'Transformação completa: Lavagem técnica e proteção de pintura.' },
                        { videoId: 'L_jWHffIx5E', title: 'Review Harley Davidson Fat Boy', description: 'Análise técnica de suspensão e performance desta lenda.' },
                        { videoId: '7nS_a_8y7mE', title: 'Ajuste de Válvulas Precision', description: 'Por que o ajuste correto é vital para a longevidade do motor.' }
                    ]);
                }
            } catch (err) {
                console.error(err);
            }
        };
        fetchVideos();
    }, []);

    const featuredVideo = videos.length > 0 ? videos[0] : null;
    const remainingVideos = videos.slice(1);

    return (
        <section id="two" style={{
            position: 'relative',
            backgroundColor: '#0f1016',
            color: '#ffffff',
            overflow: 'hidden',
            padding: '100px 0',
            fontFamily: "'Inter', sans-serif"
        }}>
            {/* Background Decorative Elements */}
            <div style={{
                position: 'absolute',
                top: '-10%',
                right: '-5%',
                width: '40%',
                height: '60%',
                background: 'radial-gradient(circle, rgba(228, 76, 101, 0.08) 0%, transparent 70%)',
                zIndex: 1,
                pointerEvents: 'none'
            }} />
            <div style={{
                position: 'absolute',
                bottom: '-10%',
                left: '-5%',
                width: '40%',
                height: '60%',
                background: 'radial-gradient(circle, rgba(228, 76, 101, 0.05) 0%, transparent 70%)',
                zIndex: 1,
                pointerEvents: 'none'
            }} />

            <div className="container" style={{ position: 'relative', zIndex: 2 }}>
                <header style={{
                    marginBottom: '60px',
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'flex-end',
                    flexWrap: 'wrap',
                    gap: '20px'
                }}>
                    <div style={{ maxWidth: '600px' }}>
                        <div style={{
                            fontSize: '0.9rem',
                            textTransform: 'uppercase',
                            letterSpacing: '4px',
                            color: '#e44c65',
                            fontWeight: '700',
                            marginBottom: '10px'
                        }}>Subrider Garage</div>
                        <h2 style={{
                            fontSize: 'clamp(2.5rem, 5vw, 3.5rem)',
                            fontWeight: '900',
                            lineHeight: '1.1',
                            margin: 0,
                            letterSpacing: '-1px',
                            color: 'white'
                        }}>Galeria de <span style={{ color: '#e44c65' }}>Vídeos</span></h2>
                        <p style={{
                            fontSize: '1.1rem',
                            color: 'rgba(255,255,255,0.6)',
                            marginTop: '20px',
                            lineHeight: '1.6'
                        }}>
                            Acompanhe os bastidores da nossa oficina, dicas técnicas e os projetos mais incríveis que passam pela Subrider.
                        </p>
                    </div>
                    <a
                        href="https://www.youtube.com/channel/UC_rUL6tWuwx-iACNG_uHZVA?sub_confirmation=1"
                        target="_blank"
                        className="btn-subscribe"
                        style={{
                            display: 'flex',
                            alignItems: 'center',
                            gap: '10px',
                            backgroundColor: '#ff0000',
                            color: 'white',
                            padding: '12px 24px',
                            borderRadius: '50px',
                            fontWeight: 'bold',
                            textDecoration: 'none',
                            transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
                            boxShadow: '0 4px 20px rgba(255, 0, 0, 0.3)'
                        }}
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 4-8 4z" />
                        </svg>
                        INSCREVA-SE NO CANAL
                    </a>
                </header>

                <div style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(12, 1fr)',
                    gap: '30px'
                }}>
                    {/* Featured Video Card */}
                    {featuredVideo && (
                        <div style={{
                            gridColumn: 'span 8',
                            position: 'relative',
                            borderRadius: '24px',
                            overflow: 'hidden',
                            aspectRatio: '16/9',
                            boxShadow: '0 30px 60px rgba(0,0,0,0.5)',
                            backgroundColor: '#1a1b23'
                        }} className="featured-card">
                            <a
                                href={`https://www.youtube.com/watch?v=${featuredVideo.videoId}`}
                                target="_blank"
                                style={{ display: 'block', height: '100%', position: 'relative' }}
                            >
                                <img
                                    src={`https://i.ytimg.com/vi/${featuredVideo.videoId}/maxresdefault.jpg`}
                                    alt={featuredVideo.title}
                                    style={{
                                        width: '100%',
                                        height: '100%',
                                        objectFit: 'cover',
                                        transition: 'transform 0.5s ease'
                                    }}
                                    className="featured-img"
                                />
                                <div style={{
                                    position: 'absolute',
                                    inset: 0,
                                    background: 'linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 50%, transparent 100%)',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    justifyContent: 'flex-end',
                                    padding: '40px'
                                }}>
                                    <span style={{
                                        backgroundColor: '#e44c65',
                                        color: 'white',
                                        padding: '4px 12px',
                                        borderRadius: '4px',
                                        fontSize: '0.8rem',
                                        fontWeight: 'bold',
                                        width: 'fit-content',
                                        marginBottom: '15px'
                                    }}>DESTAQUE</span>
                                    <h3 style={{
                                        fontSize: 'clamp(1.5rem, 3vw, 2.5rem)',
                                        fontWeight: '800',
                                        margin: '0 0 10px 0',
                                        color: 'white',
                                        lineHeight: '1.2'
                                    }}>{featuredVideo.title}</h3>
                                    <p style={{
                                        fontSize: '1rem',
                                        color: 'rgba(255,255,255,0.7)',
                                        margin: 0,
                                        maxWidth: '80%',
                                        display: '-webkit-box',
                                        WebkitLineClamp: 2,
                                        WebkitBoxOrient: 'vertical',
                                        overflow: 'hidden'
                                    }}>{featuredVideo.description}</p>
                                </div>
                                <div className="play-btn">
                                    <svg viewBox="0 0 24 24" width="48" height="48" fill="white">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </div>
                            </a>
                        </div>
                    )}

                    {/* Small Cards Grid */}
                    <div style={{
                        gridColumn: 'span 4',
                        display: 'flex',
                        flexDirection: 'column',
                        gap: '20px'
                    }} className="side-grid">
                        {remainingVideos.slice(0, 3).map((video, idx) => (
                            <div
                                key={video.videoId}
                                onMouseEnter={() => setHoveredId(video.videoId)}
                                onMouseLeave={() => setHoveredId(null)}
                                style={{
                                    display: 'flex',
                                    gap: '15px',
                                    backgroundColor: 'rgba(255,255,255,0.03)',
                                    padding: '12px',
                                    borderRadius: '16px',
                                    border: '1px solid rgba(255,255,255,0.05)',
                                    transition: 'all 0.3s ease',
                                    cursor: 'pointer',
                                    transform: hoveredId === video.videoId ? 'translateX(10px)' : 'none',
                                    boxShadow: hoveredId === video.videoId ? '0 10px 30px rgba(0,0,0,0.3)' : 'none',
                                    backdropFilter: 'blur(5px)'
                                }}
                            >
                                <a
                                    href={`https://www.youtube.com/watch?v=${video.videoId}`}
                                    target="_blank"
                                    style={{
                                        display: 'block',
                                        width: '120px',
                                        height: '80px',
                                        flexShrink: 0,
                                        position: 'relative'
                                    }}
                                >
                                    <img
                                        src={`https://i.ytimg.com/vi/${video.videoId}/mqdefault.jpg`}
                                        alt={video.title}
                                        style={{
                                            width: '100%',
                                            height: '100%',
                                            objectFit: 'cover',
                                            borderRadius: '10px'
                                        }}
                                    />
                                    <div style={{
                                        position: 'absolute',
                                        inset: 0,
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        backgroundColor: 'rgba(0,0,0,0.2)',
                                        borderRadius: '10px',
                                        opacity: hoveredId === video.videoId ? 1 : 0,
                                        transition: 'opacity 0.3s'
                                    }}>
                                        <svg viewBox="0 0 24 24" width="24" height="24" fill="white">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </div>
                                </a>
                                <div style={{ flex: 1, minWidth: 0 }}>
                                    <h4 style={{
                                        fontSize: '0.9rem',
                                        fontWeight: '600',
                                        margin: '0 0 5px 0',
                                        color: hoveredId === video.videoId ? '#e44c65' : 'white',
                                        transition: 'color 0.3s',
                                        display: '-webkit-box',
                                        WebkitLineClamp: 2,
                                        WebkitBoxOrient: 'vertical',
                                        overflow: 'hidden'
                                    }}>{video.title}</h4>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '5px' }}>
                                        <div style={{ width: '4px', height: '4px', borderRadius: '50%', backgroundColor: '#e44c65' }}></div>
                                        <span style={{ fontSize: '0.75rem', color: 'rgba(255,255,255,0.4)' }}>Sugerido</span>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Bottom Stats/Info Bar */}
                <div style={{
                    marginTop: '60px',
                    padding: '30px',
                    backgroundColor: 'rgba(255,255,255,0.02)',
                    borderRadius: '24px',
                    border: '1px solid rgba(255,255,255,0.05)',
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
                    gap: '40px'
                }}>
                    <div style={{ textAlign: 'center' }}>
                        <span style={{ fontSize: '2rem', fontWeight: '900', color: 'white', display: 'block' }}>10k+</span>
                        <span style={{ fontSize: '0.8rem', color: 'rgba(255,255,255,0.5)', textTransform: 'uppercase', letterSpacing: '1px' }}>Inscritos</span>
                    </div>
                    <div style={{ textAlign: 'center', borderLeft: '1px solid rgba(255,255,255,0.1)', borderRight: '1px solid rgba(255,255,255,0.1)' }}>
                        <span style={{ fontSize: '2rem', fontWeight: '900', color: 'white', display: 'block' }}>500+</span>
                        <span style={{ fontSize: '0.8rem', color: 'rgba(255,255,255,0.5)', textTransform: 'uppercase', letterSpacing: '1px' }}>Vídeos Técnicos</span>
                    </div>
                    <div style={{ textAlign: 'center' }}>
                        <span style={{ fontSize: '2rem', fontWeight: '900', color: 'white', display: 'block' }}>1M+</span>
                        <span style={{ fontSize: '0.8rem', color: 'rgba(255,255,255,0.5)', textTransform: 'uppercase', letterSpacing: '1px' }}>Visualizações</span>
                    </div>
                </div>
            </div>

            <style jsx>{`
                .featured-card:hover .featured-img {
                    transform: scale(1.05);
                }
                .play-btn {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 80px;
                    height: 80px;
                    background: rgba(228, 76, 101, 0.9);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 0 40px rgba(228, 76, 101, 0.4);
                    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                    opacity: 0.8;
                }
                .featured-card:hover .play-btn {
                    transform: translate(-50%, -50%) scale(1.1);
                    opacity: 1;
                    background: #e44c65;
                    box-shadow: 0 0 60px rgba(228, 76, 101, 0.6);
                    animation: pulse 2s infinite;
                }
                @keyframes pulse {
                    0% { box-shadow: 0 0 0 0 rgba(228, 76, 101, 0.7); }
                    70% { box-shadow: 0 0 0 20px rgba(228, 76, 101, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(228, 76, 101, 0); }
                }
                .btn-subscribe:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 30px rgba(255, 0, 0, 0.5);
                    background-color: #d00000;
                }
                @media (max-width: 1024px) {
                    .side-grid {
                        grid-column: span 12 !important;
                        flex-direction: row !important;
                        flex-wrap: wrap;
                    }
                    .side-grid > div {
                        flex: 1 1 300px;
                    }
                    .featured-card {
                        grid-column: span 12 !important;
                    }
                }
                @media (max-width: 768px) {
                    header {
                        justify-content: center;
                        text-align: center;
                    }
                    .btn-subscribe {
                        width: 100%;
                        justify-content: center;
                    }
                }
            `}</style>
        </section>
    );
}
