'use client';

export default function Mapa() {
    const whatsappNumber = "5561981282136";
    const address = "Quadra 12, conjunto L, St. Sul, Brasília - DF, 72415-612";
    const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent("Sub-Rider Quadra 12 conjunto L St. Sul Brasília")}`;

    return (
        <section id="one" style={{
            backgroundColor: '#1c1d26',
            position: 'relative',
            padding: '80px 0',
            color: '#fff',
            overflow: 'hidden'
        }}>
            {/* Background Decorative Element */}
            <div style={{
                position: 'absolute',
                top: '-10%',
                right: '-5%',
                width: '400px',
                height: '400px',
                background: 'radial-gradient(circle, rgba(228, 76, 101, 0.1) 0%, transparent 70%)',
                zIndex: 0
            }}></div>

            <div className="container" style={{ position: 'relative', zIndex: 1 }}>
                <header style={{ textAlign: 'center', marginBottom: '60px' }}>
                    <h2 style={{
                        fontSize: '3rem',
                        fontWeight: '800',
                        marginBottom: '10px',
                        background: 'linear-gradient(45deg, #fff, #e44c65)',
                        WebkitBackgroundClip: 'text',
                        WebkitTextFillColor: 'transparent',
                        textTransform: 'uppercase',
                        letterSpacing: '2px'
                    }}>
                        Localização
                    </h2>
                    <p style={{ opacity: 0.7, fontSize: '1.2rem' }}>Venha nos visitar ou entre em contato</p>
                </header>

                <div style={{
                    display: 'flex',
                    flexWrap: 'wrap',
                    gap: '40px',
                    alignItems: 'stretch'
                }}>
                    {/* Map Column */}
                    <div style={{
                        flex: '1 1 500px',
                        minHeight: '400px',
                        borderRadius: '24px',
                        overflow: 'hidden',
                        boxShadow: '0 20px 50px rgba(0,0,0,0.3)',
                        border: '1px solid rgba(255,255,255,0.05)'
                    }}>
                        <iframe
                            width="100%"
                            height="100%"
                            id="gmap_canvas"
                            src="https://maps.google.com/maps?q=brazil%20subrider&t=&z=17&ie=UTF8&iwloc=&output=embed"
                            frameBorder="0"
                            scrolling="no"
                            marginHeight="0"
                            marginWidth="0"
                            style={{ filter: 'grayscale(0.2) contrast(1.1) invert(0.9) hue-rotate(180deg)', border: 0, display: 'block' }}
                        ></iframe>
                    </div>

                    {/* Info Column */}
                    <div style={{
                        flex: '1 1 350px',
                        display: 'flex',
                        flexDirection: 'column',
                        justifyContent: 'center',
                        gap: '30px'
                    }}>
                        {/* Address Card */}
                        <div style={{
                            background: 'rgba(255, 255, 255, 0.03)',
                            backdropFilter: 'blur(10px)',
                            padding: '30px',
                            borderRadius: '24px',
                            border: '1px solid rgba(255,255,255,0.05)',
                            transition: 'all 0.3s ease'
                        }}>
                            <div style={{ display: 'flex', alignItems: 'flex-start', gap: '20px' }}>
                                <div style={{
                                    background: '#e44c65',
                                    width: '50px',
                                    height: '50px',
                                    borderRadius: '12px',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    flexShrink: 0
                                }}>
                                    <i className="fas fa-map-marker-alt" style={{ fontSize: '1.5rem' }}></i>
                                </div>
                                <div>
                                    <h3 style={{ margin: '0 0 10px 0', fontSize: '1.4rem' }}>Endereço</h3>
                                    <p style={{ margin: 0, opacity: 0.8, lineHeight: '1.6' }}>
                                        {address}
                                    </p>
                                    <a
                                        href={mapsUrl}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        style={{
                                            display: 'inline-block',
                                            marginTop: '15px',
                                            color: '#e44c65',
                                            textDecoration: 'none',
                                            fontWeight: '600',
                                            fontSize: '0.9rem',
                                            borderBottom: '1px solid transparent',
                                            transition: 'all 0.3s ease'
                                        }}
                                        onMouseOver={(e) => e.currentTarget.style.borderBottomColor = '#e44c65'}
                                        onMouseOut={(e) => e.currentTarget.style.borderBottomColor = 'transparent'}
                                    >
                                        Abrir no Google Maps <i className="fas fa-external-link-alt" style={{ fontSize: '0.8rem', marginLeft: '5px' }}></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {/* WhatsApp Card */}
                        <div style={{
                            background: 'rgba(37, 211, 102, 0.05)',
                            backdropFilter: 'blur(10px)',
                            padding: '30px',
                            borderRadius: '24px',
                            border: '1px solid rgba(37, 211, 102, 0.2)',
                            transition: 'all 0.3s ease'
                        }}>
                            <div style={{ display: 'flex', alignItems: 'flex-start', gap: '20px' }}>
                                <div style={{
                                    background: '#25D366',
                                    width: '50px',
                                    height: '50px',
                                    borderRadius: '12px',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    flexShrink: 0
                                }}>
                                    <i className="fab fa-whatsapp" style={{ fontSize: '1.8rem' }}></i>
                                </div>
                                <div>
                                    <h3 style={{ margin: '0 0 10px 0', fontSize: '1.4rem' }}>WhatsApp</h3>
                                    <p style={{ margin: '0 0 20px 0', opacity: 0.8 }}>(61) 98128-2136</p>
                                    <a
                                        href={`https://wa.me/${whatsappNumber}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        style={{
                                            display: 'inline-flex',
                                            alignItems: 'center',
                                            gap: '10px',
                                            background: '#25D366',
                                            color: '#fff',
                                            padding: '12px 25px',
                                            borderRadius: '50px',
                                            textDecoration: 'none',
                                            fontWeight: '700',
                                            boxShadow: '0 10px 20px rgba(37, 211, 102, 0.2)',
                                            transition: 'transform 0.3s ease, boxShadow 0.3s ease'
                                        }}
                                        onMouseOver={(e) => {
                                            e.currentTarget.style.transform = 'translateY(-2px)';
                                            e.currentTarget.style.boxShadow = '0 15px 30px rgba(37, 211, 102, 0.3)';
                                        }}
                                        onMouseOut={(e) => {
                                            e.currentTarget.style.transform = 'translateY(0)';
                                            e.currentTarget.style.boxShadow = '0 10px 20px rgba(37, 211, 102, 0.2)';
                                        }}
                                    >
                                        Conversar agora
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Scroll Indicator */}
            <a href="#two" className="goto-next scrolly" style={{
                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                borderRadius: '100%',
                bottom: '20px',
                color: '#ffffff',
                display: 'block',
                height: '50px',
                left: '50%',
                lineHeight: '55px',
                marginLeft: '-25px',
                position: 'absolute',
                textAlign: 'center',
                width: '50px',
                zIndex: 3,
                border: '1px solid rgba(255,255,255,0.1)',
                textDecoration: 'none',
                transition: 'all 0.3s ease'
            }}
                onMouseOver={(e) => e.currentTarget.style.backgroundColor = 'rgba(255,255,255,0.1)'}
                onMouseOut={(e) => e.currentTarget.style.backgroundColor = 'rgba(255,255,255,0.05)'}
            >
                <i className="fas fa-chevron-down"></i>
            </a>
        </section>
    );
}

