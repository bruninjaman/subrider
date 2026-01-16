'use client';

import Link from 'next/link';
import { useSearchParams } from 'next/navigation';

export default function RelatorioHeader() {
    const searchParams = useSearchParams();
    const ordemId = searchParams.get('ordem');

    // Encode ordemId for URL just in case, though usually not needed for simple IDs
    // If ordemId is like "2024/001", href should be "/ordemservico/2024/001"
    const backLink = ordemId ? `/ordemservico/${ordemId}` : '/tabelaOrdens';

    return (
        <header style={{
            background: '#1c1d26',
            padding: '1.2rem 2rem',
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            borderBottom: '1px solid rgba(255,255,255,0.05)',
            position: 'fixed',
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
                    <li>
                        <Link
                            href={backLink}
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
                                height: 'auto',
                                textDecoration: 'none'
                            }}
                        >
                            Voltar
                        </Link>
                    </li>
                </ul>
            </nav>
        </header>
    );
}
