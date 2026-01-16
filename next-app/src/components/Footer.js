'use client';

export default function Footer() {
    const whatsappLink = "https://wa.me/5561981282136";

    return (
        <footer id="footer" style={{
            padding: '80px 0',
            background: '#14151a',
            textAlign: 'center',
            borderTop: '1px solid rgba(255,255,255,0.03)',
            color: 'rgba(255,255,255,0.6)'
        }}>
            <div className="container">
                <div style={{ marginBottom: '40px' }}>
                    <ul className="icons" style={{
                        display: 'flex',
                        justifyContent: 'center',
                        gap: '2.5rem',
                        listStyle: 'none',
                        padding: 0,
                    }}>
                        <li>
                            <a href="https://www.youtube.com/channel/UC_rUL6tWuwx-iACNG_uHZVA?sub_confirmation=1"
                                target="_blank"
                                rel="noopener noreferrer"
                                style={{
                                    fontSize: '1.8rem',
                                    color: 'rgba(255,255,255,0.4)',
                                    transition: 'all 0.3s ease'
                                }}
                                onMouseOver={(e) => e.currentTarget.style.color = '#ff0000'}
                                onMouseOut={(e) => e.currentTarget.style.color = 'rgba(255,255,255,0.4)'}
                            >
                                <i className="fab fa-youtube"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/subriderbsb/"
                                target="_blank"
                                rel="noopener noreferrer"
                                style={{
                                    fontSize: '1.8rem',
                                    color: 'rgba(255,255,255,0.4)',
                                    transition: 'all 0.3s ease'
                                }}
                                onMouseOver={(e) => e.currentTarget.style.color = '#e1306c'}
                                onMouseOut={(e) => e.currentTarget.style.color = 'rgba(255,255,255,0.4)'}
                            >
                                <i className="fab fa-instagram"></i>
                            </a>
                        </li>
                        <li>
                            <a href={whatsappLink}
                                target="_blank"
                                rel="noopener noreferrer"
                                style={{
                                    fontSize: '1.8rem',
                                    color: 'rgba(255,255,255,0.4)',
                                    transition: 'all 0.3s ease'
                                }}
                                onMouseOver={(e) => e.currentTarget.style.color = '#25D366'}
                                onMouseOut={(e) => e.currentTarget.style.color = 'rgba(255,255,255,0.4)'}
                            >
                                <i className="fab fa-whatsapp"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <div style={{
                    display: 'flex',
                    flexDirection: 'column',
                    gap: '15px',
                    fontSize: '0.95rem',
                    fontWeight: '300',
                    letterSpacing: '0.5px'
                }}>
                    <p style={{ margin: 0 }}>
                        <a href={whatsappLink}
                            target="_blank"
                            rel="noopener noreferrer"
                            style={{
                                color: 'inherit',
                                textDecoration: 'none',
                                display: 'inline-flex',
                                alignItems: 'center',
                                gap: '8px',
                                transition: 'color 0.3s ease'
                            }}
                            onMouseOver={(e) => e.currentTarget.style.color = '#25D366'}
                            onMouseOut={(e) => e.currentTarget.style.color = 'inherit'}
                        >
                            <i className="fab fa-whatsapp" style={{ color: '#25D366' }}></i>
                            <strong style={{ color: '#fff' }}>WhatsApp:</strong> (61) 98128-2136
                        </a>
                    </p>
                    <p style={{ margin: 0 }}>
                        <strong>Responsável:</strong> Robson Alexandre
                    </p>
                    <div style={{
                        marginTop: '20px',
                        paddingTop: '30px',
                        borderTop: '1px solid rgba(255,255,255,0.03)',
                        opacity: 0.4,
                        fontSize: '0.8rem'
                    }}>
                        &copy; {new Date().getFullYear()} Sub-Rider. Todos os direitos reservados.
                    </div>
                </div>
            </div>
        </footer>
    );
}

