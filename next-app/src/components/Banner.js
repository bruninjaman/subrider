'use client';

export default function Banner() {
    return (
        <section id="banner" style={{
            backgroundAttachment: 'fixed',
            backgroundColor: '#1c1d26',
            backgroundImage: 'url("/assets/css/images/banner.jpg")', // Original used banner.jpg
            backgroundPosition: 'center',
            backgroundRepeat: 'no-repeat',
            backgroundSize: 'cover',
            color: '#ffffff',
            cursor: 'default',
            position: 'relative',
            textAlign: 'center',
            zIndex: 1,
            minHeight: '100vh',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
        }}>
            <div className="content" style={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                width: '100%',
                maxWidth: '1200px',
                padding: '2em',
                position: 'relative',
                zIndex: 2,
                flexWrap: 'wrap-reverse'
            }}>
                <header style={{
                    borderBottom: 'none',
                    textAlign: 'right',
                    flex: '1',
                    minWidth: '300px',
                    margin: '1em'
                }}>
                    <h2 style={{
                        fontSize: '3.5em',
                        color: '#ffffff',
                        fontWeight: '300',
                        marginBottom: '0.25em',
                        lineHeight: '1.2'
                    }}>Mecânica Especializada em motocicletas</h2>
                    <p style={{
                        fontSize: '1.25em',
                        opacity: '0.75',
                        margin: '0'
                    }}>Oficina multimarcas incluindo HD.<br /> Entre em contato conosco e conheça nossos serviços.</p>
                </header>
                <div className="image" style={{
                    flex: '0 0 18em',
                    height: '18em',
                    width: '18em',
                    marginLeft: '3em',
                    borderRadius: '100%',
                    overflow: 'hidden',
                    border: 'solid 8px rgba(255, 255, 255, 0.1)',
                    margin: '1em'
                }}>
                    <img src="/assets/css/images/Close.jpg" alt="" style={{
                        width: '100%',
                        height: '100%',
                        objectFit: 'cover'
                    }} />
                </div>
            </div>

            <a href="#one" className="goto-next scrolly" style={{
                bottom: '2em',
                left: '50%',
                marginLeft: '-1em',
                position: 'absolute',
                zIndex: 3,
                width: '2em',
                height: '1.5em',
                textIndent: '10em',
                overflow: 'hidden',
                whiteSpace: 'nowrap',
                backgroundImage: 'url("/assets/css/images/arrow.svg")',
                backgroundPosition: 'center',
                backgroundRepeat: 'no-repeat',
                backgroundSize: 'contain',
                opacity: '0.5',
                transition: 'opacity 0.2s'
            }}>Proximo</a>

            <div style={{
                backgroundColor: 'rgba(23, 24, 32, 0.8)',
                content: "''",
                display: 'block',
                height: '100%',
                left: 0,
                position: 'absolute',
                top: 0,
                width: '100%',
                zIndex: 1
            }}></div>
        </section>
    );
}

