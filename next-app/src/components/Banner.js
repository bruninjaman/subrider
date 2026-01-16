'use client';

export default function Banner() {
    return (
        <section id="banner" style={{
            padding: '14em 0 14em 0',
            backgroundAttachment: 'fixed',
            backgroundColor: '#1c1d26',
            backgroundImage: 'url("/assets/css/images/Close.jpg")',
            backgroundPosition: 'center',
            backgroundRepeat: 'no-repeat',
            backgroundSize: 'cover',
            color: '#ffffff',
            cursor: 'default',
            position: 'relative',
            textAlign: 'center',
            zIndex: 1
        }}>
            <div className="content" style={{
                display: 'inline-block',
                verticalAlign: 'middle',
                width: '60%',
                position: 'relative',
                zIndex: 2
            }}>
                <header style={{ borderBottom: 'none' }}>
                    <h2 style={{ fontSize: '3.5em', color: '#ffffff', fontWeight: '300', marginBottom: '0.25em' }}>Mecânica Especializada em motocicletas</h2>
                    <p style={{ fontSize: '1.25em', opacity: '0.75' }}>Oficina multimarcas incluindo HD.<br /> Entre em contato conosco e conheça nossos serviços.</p>
                </header>
            </div>
            <a href="#one" className="goto-next scrolly" style={{
                backgroundColor: 'rgba(255, 255, 255, 0.1)',
                borderRadius: '100%',
                bottom: '4em',
                color: '#ffffff',
                display: 'block',
                height: '4em',
                left: '50%',
                lineHeight: '4.5em',
                marginLeft: '-2em',
                position: 'absolute',
                textAlign: 'center',
                width: '4em',
                zIndex: 3,
                borderBottom: 'none'
            }}>Proximo</a>
            <div style={{
                backgroundColor: 'rgba(0, 0, 0, 0.5)',
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
