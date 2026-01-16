import { cookies } from 'next/headers';
import { NextResponse } from 'next/server';

export async function GET() {
    const cookieStore = await cookies();
    cookieStore.delete('user');
    cookieStore.delete('type');

    return NextResponse.redirect(new URL('/login', process.env.NEXT_PUBLIC_BASE_ADDRESS || 'http://localhost:3000'));
}
