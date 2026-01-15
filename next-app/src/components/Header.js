import Link from 'next/link';

export default function Header() {
    return (
        <header id="header" style={{
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            padding: '10px 60px',
            backgroundColor: '#1c1d26', // Solid dark color matching the theme
            borderBottom: '2px solid rgba(255, 255, 255, 0.05)',
            position: 'sticky',
            top: 0,
            zIndex: 1000,
            height: '80px'
        }}>
            <div id="logo">
                <Link href="/">
                    <img src="/assets/css/images/logo-branco-crop.png" alt="Subrider Logo" style={{ height: '55px', width: 'auto' }} />
                </Link>
            </div>
            <nav id="nav">
                <ul style={{ listStyle: 'none', margin: 0, padding: 0 }}>
                    <li>
                        <Link href="/" style={{
                            color: '#e44c65',
                            textDecoration: 'none',
                            fontSize: '1rem',
                            fontWeight: '400',
                            transition: 'opacity 0.2s'
                        }}>Voltar</Link>
                    </li>
                </ul>
            </nav>
        </header>
    );
}
