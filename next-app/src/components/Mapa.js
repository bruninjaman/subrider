'use client';

export default function Mapa() {
    return (
        <section id="one" className="spotlight style1 bottom" style={{
            backgroundColor: '#272833',
            position: 'relative',
            padding: '0'
        }}>
            <span className="image fit main" style={{
                display: 'block',
                position: 'relative',
                width: '100%',
                margin: 0
            }}>
                <img src="/assets/css/images/mapa subrider.jpg" alt="" style={{
                    width: '100%',
                    maxHeight: '40vh',
                    objectFit: 'cover',
                    display: 'block'
                }} />
                <h1 className="localizacao" style={{
                    position: 'absolute',
                    top: '50%',
                    left: '2rem',
                    transform: 'translateY(-50%)',
                    fontSize: '4rem',
                    fontWeight: '800',
                    color: 'white',
                    textShadow: '2px 2px 10px rgba(0,0,0,0.5)',
                    margin: 0
                }}>Localização</h1>
            </span>
            <div className="content" style={{
                padding: '4em 0',
                background: '#272833'
            }}>
                <div className="container" style={{
                    display: 'flex',
                    flexWrap: 'wrap',
                    gap: '2rem',
                    justifyContent: 'space-between',
                    alignItems: 'center'
                }}>
                    <div style={{ flex: '1 1 500px' }}>
                        <div className="mapouter" style={{
                            position: 'relative',
                            height: '300px',
                            width: '100%',
                            borderRadius: '12px',
                            overflow: 'hidden'
                        }}>
                            <iframe
                                width="100%"
                                height="300"
                                id="gmap_canvas"
                                src="https://maps.google.com/maps?q=brazil%20subrider&t=&z=17&ie=UTF8&iwloc=&output=embed"
                                frameBorder="0"
                                scrolling="no"
                                marginHeight="0"
                                marginWidth="0"
                            ></iframe>
                        </div>
                    </div>
                    <div style={{ flex: '1 1 300px', fontSize: '1.2rem' }}>
                        <p>Endereço da Sub-Rider, Quadra 12, conjunto L, St. Sul, Brasília - DF. CEP 72415-612.</p>
                        <p style={{ marginTop: '1rem' }}>
                            <b style={{ color: '#e44c65' }}>Whatsapp:</b> (61) 98128-2136
                        </p>
                    </div>
                </div>
            </div>
            <a href="#two" className="goto-next scrolly" style={{
                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                borderRadius: '100%',
                bottom: '2em',
                color: '#ffffff',
                display: 'block',
                height: '3em',
                left: '50%',
                lineHeight: '3.5em',
                marginLeft: '-1.5em',
                position: 'absolute',
                textAlign: 'center',
                width: '3em',
                zIndex: 3,
                borderBottom: 'none'
            }}>Proximo</a>
        </section>
    );
}
