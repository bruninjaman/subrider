'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useState, useEffect } from 'react';

export default function Header() {
    const pathname = usePathname();
    const [session, setSession] = useState({ loggedIn: false, user: null });

    useEffect(() => {
        const checkSession = async () => {
            try {
                const res = await fetch('/api/session');
                if (res.ok) {
                    const data = await res.json();
                    setSession(data);
                }
            } catch (err) {
                console.error('Session check failed:', err);
            }
        };
        checkSession();
    }, [pathname]);

    let navLinks = [
        { name: 'Motocicletas', href: '/tabelaMotos' },
        { name: 'Peças', href: '/tabelaPecas' },
        { name: 'Serviços', href: '/tabelaServicos' },
    ];

    if (pathname === '/tabelaMotos') {
        navLinks[0] = { name: 'Ordens de Serviço', href: '/tabelaOrdens' };
    } else if (pathname === '/tabelaPecas') {
        navLinks[1] = { name: 'Ordens de Serviço', href: '/tabelaOrdens' };
    } else if (pathname === '/tabelaServicos') {
        navLinks[2] = { name: 'Ordens de Serviço', href: '/tabelaOrdens' };
    }

    // Exibe o header mesmo na página de login, mas com opções simplificadas

    return (
        <header style={{
            background: pathname === '/login' ? 'transparent' : '#1c1d26',
            padding: '1.2rem 2rem',
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            borderBottom: pathname === '/login' ? 'none' : '1px solid rgba(255,255,255,0.05)',
            position: pathname === '/login' ? 'absolute' : 'sticky',
            top: 0,
            left: 0,
            right: 0,
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


                    {pathname === '/login' && (
                        <li>
                            <Link
                                href="/#four"
                                style={{
                                    color: 'rgba(255,255,255,0.75)',
                                    fontSize: '1rem',
                                    fontWeight: '300',
                                    transition: 'color 0.2s ease',
                                    border: 'none',
                                    padding: '5px 0',
                                    textTransform: 'none'
                                }}
                            >
                                Nossos Serviços
                            </Link>
                        </li>
                    )}

                    {session.loggedIn && navLinks.map((link) => (
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
                            >
                                {link.name}
                            </Link>
                        </li>
                    ))}

                    <li>
                        {session.loggedIn ? (
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
                            >
                                Sair
                            </Link>
                        ) : (
                            <Link
                                href={pathname === '/login' ? '/' : '/login'}
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
                            >
                                {pathname === '/login' ? 'Voltar' : 'Entrar'}
                            </Link>
                        )}
                    </li>
                </ul>
            </nav>
        </header>
    );
}
