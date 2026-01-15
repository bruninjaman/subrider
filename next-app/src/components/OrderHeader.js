import { formatDate } from '@/lib/utils';
import Link from 'next/link';

export default function OrderHeader({ id, date, owner, km }) {
    return (
        <section id="banner" style={{
            padding: '3em 0',
            textAlign: 'center',
            background: 'linear-gradient(rgba(28, 29, 38, 0.8), rgba(28, 29, 38, 0.8)), url("/assets/css/images/banner.jpg")',
            backgroundSize: 'cover',
            backgroundPosition: 'center',
            borderRadius: '20px',
            margin: '40px 0',
            boxShadow: '0 10px 40px rgba(0,0,0,0.5)',
            border: '1px solid rgba(255,255,255,0.05)'
        }}>
            <div className="content">
                <Link href={`/ordemservico/${id}`} style={{ textDecoration: 'none', color: 'inherit' }}>
                    <h2 style={{
                        fontSize: '3.5rem',
                        fontWeight: '800',
                        color: 'white',
                        marginBottom: '10px',
                        textShadow: '0 4px 15px rgba(0,0,0,1)',
                        cursor: 'pointer',
                        transition: 'transform 0.2s ease'
                    }}
                        onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.02)'}
                        onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                    >ORDEM Nº {id}</h2>
                </Link>

                <div style={{
                    display: 'flex',
                    justifyContent: 'center',
                    gap: '40px',
                    marginTop: '30px',
                    color: '#e44c65',
                    fontWeight: '600',
                    textTransform: 'uppercase',
                    letterSpacing: '2px'
                }}>
                    <div>
                        <span style={{ color: 'rgba(255,255,255,0.5)', fontSize: '0.8rem', display: 'block' }}>DATA</span>
                        <span style={{ fontSize: '1.2rem' }}>{formatDate(date)}</span>
                    </div>
                    <div style={{ borderLeft: '1px solid rgba(255,255,255,0.1)', paddingLeft: '40px' }}>
                        <span style={{ color: 'rgba(255,255,255,0.5)', fontSize: '0.8rem', display: 'block' }}>PROPRIETÁRIO</span>
                        <span style={{ fontSize: '1.2rem', color: 'white' }}>{owner || 'NÃO DEFINIDO'}</span>
                    </div>
                    <div style={{ borderLeft: '1px solid rgba(255,255,255,0.1)', paddingLeft: '40px' }}>
                        <span style={{ color: 'rgba(255,255,255,0.5)', fontSize: '0.8rem', display: 'block' }}>QUILOMETRAGEM</span>
                        <span style={{ fontSize: '1.2rem' }}>{km || '---'} KM</span>
                    </div>
                </div>
            </div>
        </section>
    );
}
