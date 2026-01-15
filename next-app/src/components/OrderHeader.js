import { formatDate } from '@/lib/utils';
import Link from 'next/link';
import { useState } from 'react';

export default function OrderHeader({ id, date, owner, km }) {
    const [isEditing, setIsEditing] = useState(null); // 'date', 'owner', 'km'
    const [values, setValues] = useState({ date, owner, km });

    const handleUpdate = async (field, value) => {
        try {
            const res = await fetch(`/api/ordemservico/${id}`, {
                method: 'PATCH',
                body: JSON.stringify({ [field]: value }),
                headers: { 'Content-Type': 'application/json' }
            });
            if (res.ok) {
                setValues(prev => ({ ...prev, [field]: value }));
                setIsEditing(null);
            }
        } catch (err) {
            console.error('Failed to update:', err);
        }
    };

    const renderEditable = (field, label, value, type = 'text') => {
        const editing = isEditing === field;
        return (
            <div
                style={{ borderLeft: field !== 'date' ? '1px solid rgba(255,255,255,0.1)' : 'none', paddingLeft: field !== 'date' ? '40px' : '0', cursor: 'pointer' }}
                onClick={() => !editing && setIsEditing(field)}
            >
                <span style={{ color: 'rgba(255,255,255,0.5)', fontSize: '0.8rem', display: 'block' }}>{label}</span>
                {editing ? (
                    <input
                        autoFocus
                        type={type}
                        defaultValue={field === 'date' ? value?.split('T')[0] : value}
                        onBlur={(e) => handleUpdate(field, e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && handleUpdate(field, e.target.value)}
                        style={{ background: 'rgba(255,255,255,0.1)', border: '1px solid #e44c65', color: 'white', padding: '2px 8px', borderRadius: '4px', fontSize: '1.2rem', width: 'auto' }}
                    />
                ) : (
                    <span style={{ fontSize: '1.2rem', color: field === 'owner' ? 'white' : '#e44c65' }}>
                        {field === 'date' ? formatDate(values.date) : (field === 'km' ? `${values.km || '---'} KM` : (values.owner || 'NÃO DEFINIDO'))}
                    </span>
                )}
            </div>
        );
    };

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
                    fontWeight: '600',
                    textTransform: 'uppercase',
                    letterSpacing: '2px'
                }}>
                    {renderEditable('date', 'DATA', values.date, 'date')}
                    {renderEditable('owner', 'PROPRIETÁRIO', values.owner)}
                    {renderEditable('km', 'QUILOMETRAGEM', values.km, 'number')}
                </div>
            </div>
        </section>
    );
}
