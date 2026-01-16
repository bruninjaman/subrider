'use client';

import { useState } from 'react';

export default function Sobre() {
    const [hoveredIdx, setHoveredIdx] = useState(null);

    const servicos = [
        { icon: 'fa-trailer', title: 'Transporte/Reboque', desc: 'Transporte guincho para a oficina e para a residência do cliente com segurança e agilidade.' },
        { icon: 'fa-clock-rotate-left', title: 'Manutenção Preventiva', desc: 'Manutenção periódica conforme a tabela do fabricante ou por níveis: básico, intermediário e avançado.' },
        { icon: 'fa-gear', title: 'Manutenção Corretiva', desc: 'Reparo mecânico, elétrico, eletrônico e estético em motocicletas de 1 a 6 cilindros.' },
        { icon: 'fa-broom-ball', title: 'Manutenção Estética', desc: 'Pintura, polimento, recuperação de peças cromadas, soldas e detalhamento completo.' },
        { icon: 'fa-kit-medical', title: 'Manutenção Emergencial', desc: 'Atendimento de emergência para motocicletas imobilizadas com diagnóstico rápido.' },
        { icon: 'fa-coins', title: 'Manutenção Econômica', desc: 'Busca por alternativas seguras de redução de custos sem comprometer a performance.' }
    ];

    return (
        <section id="four" style={{
            padding: '8rem 0',
            background: 'linear-gradient(180deg, #1c1d26 0%, #24262e 100%)',
            position: 'relative',
            overflow: 'hidden'
        }}>
            {/* Background Decoration */}
            <div style={{
                position: 'absolute',
                top: '-10%',
                right: '-5%',
                width: '400px',
                height: '400px',
                background: 'radial-gradient(circle, rgba(228, 76, 101, 0.05) 0%, transparent 70%)',
                borderRadius: '50%',
                zIndex: 0
            }}></div>

            <div className="container" style={{ position: 'relative', zIndex: 1 }}>
                <header style={{ textAlign: 'center', marginBottom: '5rem' }}>
                    <h2 style={{
                        fontSize: '3rem',
                        fontWeight: '700',
                        marginBottom: '1rem',
                        letterSpacing: '-1px'
                    }}>
                        Nossos <span style={{ color: '#e44c65' }}>Serviços</span>
                    </h2>
                    <div style={{
                        width: '60px',
                        height: '4px',
                        backgroundColor: '#e44c65',
                        margin: '0 auto 1.5rem',
                        borderRadius: '2px'
                    }}></div>
                    <p style={{
                        fontSize: '1.2rem',
                        opacity: 0.7,
                        maxWidth: '700px',
                        margin: '0 auto',
                        lineHeight: '1.6'
                    }}>
                        Especialistas em manutenção de motocicletas de alta performance,
                        oferecendo soluções completas para manter sua máquina sempre no topo.
                    </p>
                </header>

                <div style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))',
                    gap: '2.5rem'
                }}>
                    {servicos.map((s, i) => (
                        <div
                            key={i}
                            onMouseEnter={() => setHoveredIdx(i)}
                            onMouseLeave={() => setHoveredIdx(null)}
                            style={{
                                padding: '3rem 2rem',
                                background: hoveredIdx === i ? 'rgba(255,255,255,0.06)' : 'rgba(255,255,255,0.03)',
                                border: '1px solid rgba(255,255,255,0.05)',
                                borderRadius: '24px',
                                transition: 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)',
                                transform: hoveredIdx === i ? 'translateY(-10px)' : 'translateY(0)',
                                boxShadow: hoveredIdx === i ? '0 20px 40px rgba(0,0,0,0.3), 0 0 20px rgba(228, 76, 101, 0.1)' : 'none',
                                textAlign: 'center',
                                position: 'relative',
                                cursor: 'default'
                            }}
                        >
                            <div style={{
                                width: '80px',
                                height: '80px',
                                margin: '0 auto 2rem',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                background: hoveredIdx === i ? '#e44c65' : 'rgba(228, 76, 101, 0.1)',
                                borderRadius: '20px',
                                transition: 'all 0.3s ease',
                                transform: hoveredIdx === i ? 'rotate(5deg)' : 'rotate(0deg)'
                            }}>
                                <i className={`fa-solid ${s.icon}`} style={{
                                    fontSize: '2.2rem',
                                    color: hoveredIdx === i ? '#fff' : '#e44c65',
                                    transition: 'all 0.3s ease'
                                }}></i>
                            </div>

                            <h3 style={{
                                fontSize: '1.6rem',
                                marginBottom: '1.2rem',
                                fontWeight: '500',
                                color: hoveredIdx === i ? '#e44c65' : '#fff',
                                transition: 'color 0.3s ease'
                            }}>
                                {s.title}
                            </h3>
                            <p style={{
                                opacity: 0.7,
                                lineHeight: '1.7',
                                fontSize: '1.05rem',
                                fontWeight: '300'
                            }}>
                                {s.desc}
                            </p>
                        </div>
                    ))}
                </div>

                <footer style={{ marginTop: '6rem', textAlign: 'center' }}>
                    <a
                        href="https://wa.me/5561981282136"
                        target="_blank"
                        rel="noopener noreferrer"
                        className="button"
                        style={{
                            padding: '1.2rem 3rem',
                            fontSize: '1.1rem',
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '10px'
                        }}
                    >
                        <i className="fab fa-whatsapp" style={{ fontSize: '1.4rem' }}></i>
                        Agendar Manutenção
                    </a>
                </footer>
            </div>
        </section>
    );
}
