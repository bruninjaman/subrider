'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';

export default function Header() {
    const pathname = usePathname();

    const navLinks = [
        { name: 'Ordens', href: '/' },
        { name: 'Motocicletas', href: '/tabelaMotos' },
        { name: 'Peças', href: '/tabelaPecas' },
        { name: 'Serviços', href: '/tabelaServicos' },
    ];

    return (
        <header style={{
            background: '#1c1d26',
            padding: '1.2rem 2rem',
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            borderBottom: '1px solid rgba(255,255,255,0.05)',
            position: 'sticky',
            top: 0,
            zIndex: 1000,
            backdropFilter: 'blur(10px)'
        }}>
            <div id="logo">
                <Link href="/" style={{ border: 'none' }}>
                    <img
                        src="/assets/css/images/logo-branco-crop.png"
                        alt="Subrider Logo"
                        style={{ height: '55px', width: 'auto', display: 'block' }}
                    />
                </Link>
            </div>

            <nav id="nav">
                <ul style={{
                    display: 'flex',
                    listStyle: 'none',
                    gap: '2.5rem',
                    alignItems: 'center',
                    margin: 0,
                    padding: 0
                }}>
                    {navLinks.map((link) => (
                        <li key={link.href}>
                            <Link
                                href={link.href}
                                style={{
                                    color: pathname === link.href ? '#e44c65' : 'rgba(255,255,255,0.75)',
                                    fontSize: '1rem',
                                    fontWeight: '300',
                                    transition: 'color 0.2s ease',
                                    border: 'none',
                                    padding: '5px 0',
                                    textTransform: 'none'
                                }}
                                onMouseOver={(e) => e.currentTarget.style.color = '#e44c65'}
                                onMouseOut={(e) => {
                                    if (pathname !== link.href) {
                                        e.currentTarget.style.color = 'rgba(255,255,255,0.75)';
                                    }
                                }}
                            >
                                {link.name}
                            </Link>
                        </li>
                    ))}
                    {pathname !== '/' && (
                        <li>
                            <Link
                                href="/"
                                style={{
                                    color: 'rgba(255,255,255,0.75)',
                                    fontSize: '1rem',
                                    fontWeight: '500',
                                    padding: '0.5rem 1rem',
                                    borderRadius: '4px',
                                    border: '1px solid rgba(255,255,255,0.2)',
                                    transition: 'all 0.2s',
                                    textDecoration: 'none'
                                }}
                                onMouseOver={(e) => {
                                    e.currentTarget.style.background = 'rgba(255,255,255,0.05)';
                                    e.currentTarget.style.borderColor = '#e44c65';
                                    e.currentTarget.style.color = '#e44c65';
                                }}
                                onMouseOut={(e) => {
                                    e.currentTarget.style.background = 'transparent';
                                    e.currentTarget.style.borderColor = 'rgba(255,255,255,0.2)';
                                    e.currentTarget.style.color = 'rgba(255,255,255,0.75)';
                                }}
                            >
                                ← Voltar
                            </Link>
                        </li>
                    )}
                    <li>
                        <Link
                            href="/logout"
                            className="button primary"
                            style={{
                                padding: '0.6rem 1.8rem',
                                fontSize: '1rem',
                                background: '#e44c65',
                                borderRadius: '4px',
                                color: 'white',
                                fontWeight: '400',
                                transition: 'all 0.2s ease',
                                border: 'none',
                                display: 'inline-block',
                                lineHeight: '1.5em',
                                height: 'auto'
                            }}
                            onMouseOver={(e) => {
                                e.currentTarget.style.filter = 'brightness(1.1)';
                                e.currentTarget.style.transform = 'translateY(-1px)';
                            }}
                            onMouseOut={(e) => {
                                e.currentTarget.style.filter = 'brightness(1)';
                                e.currentTarget.style.transform = 'translateY(0)';
                            }}
                        >
                            Sair
                        </Link>
                    </li>
                </ul>
            </nav>
        </header>
    );
}
