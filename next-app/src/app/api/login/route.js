import { query } from '@/lib/db';
import { cookies } from 'next/headers';
import { NextResponse } from 'next/server';

export async function POST(request) {
    try {
        const { user, pass } = await request.json();

        if (!user || !pass) {
            return NextResponse.json({ error: 'invalid_input' }, { status: 400 });
        }

        // Check if user exists
        const users = await query('SELECT * FROM login WHERE username = ?', [user]);

        if (users.length === 0) {
            return NextResponse.json({ error: 'nouser' }, { status: 404 });
        }

        const userData = users[0];

        // Check if blocked
        if (userData.blocked_until) {
            const blockedUntil = new Date(userData.blocked_until).getTime();
            const now = Date.now();

            if (now < blockedUntil) {
                const remainingMinutes = Math.ceil((blockedUntil - now) / 60000);
                return NextResponse.json({
                    error: 'blocked',
                    time: remainingMinutes
                }, { status: 403 });
            } else {
                // Reset attempts if block expired
                await query('UPDATE login SET login_attempts = 0, blocked_until = NULL WHERE username = ?', [user]);
                userData.login_attempts = 0;
            }
        }

        // Verify password (plain text as in original PHP)
        if (userData.password === pass) {
            // Success - Reset attempts
            await query('UPDATE login SET login_attempts = 0, last_attempt_time = CURRENT_TIMESTAMP WHERE username = ?', [user]);

            // Set session cookies
            const cookieStore = await cookies();
            cookieStore.set('user', userData.username, {
                httpOnly: true,
                secure: process.env.NODE_ENV === 'production',
                maxAge: 30 * 24 * 60 * 60,
                path: '/',
            });
            cookieStore.set('type', userData.userType.toString(), {
                httpOnly: true,
                secure: process.env.NODE_ENV === 'production',
                maxAge: 30 * 24 * 60 * 60,
                path: '/',
            });

            return NextResponse.json({ success: true });
        } else {
            // Wrong password - Increment attempts
            const attempts = (userData.login_attempts || 0) + 1;

            if (attempts >= 5) {
                // Block for 15 minutes
                const blockUntil = new Date(Date.now() + 15 * 60000);
                // Database expects YYYY-MM-DD HH:MM:SS format
                const blockUntilStr = blockUntil.toISOString().slice(0, 19).replace('T', ' ');

                await query('UPDATE login SET login_attempts = ?, last_attempt_time = CURRENT_TIMESTAMP, blocked_until = ? WHERE username = ?', [attempts, blockUntilStr, user]);

                return NextResponse.json({
                    error: 'blocked',
                    time: 15
                }, { status: 403 });
            } else {
                await query('UPDATE login SET login_attempts = ?, last_attempt_time = CURRENT_TIMESTAMP WHERE username = ?', [attempts, user]);

                return NextResponse.json({
                    error: 'wrong',
                    attempts: 5 - attempts
                }, { status: 401 });
            }
        }

    } catch (error) {
        console.error('Login error:', error);
        return NextResponse.json({ error: 'internal_error' }, { status: 500 });
    }
}
