'use client';
import { useEffect } from 'react';

export default function LogoutPage() {
    useEffect(() => {
        const doLogout = async () => {
            await fetch('/api/logout');
            window.location.href = '/login';
        };
        doLogout();
    }, []);

    return (
        <div style={{
            backgroundColor: '#1c1d26',
            color: 'white',
            height: '100vh',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontFamily: 'Roboto, sans-serif'
        }}>
            <h2>Saindo...</h2>
        </div>
    );
}
