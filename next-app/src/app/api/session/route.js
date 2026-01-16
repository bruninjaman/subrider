import { cookies } from 'next/headers';
import { NextResponse } from 'next/server';

export async function GET() {
    const cookieStore = await cookies();
    const user = cookieStore.get('user');
    const type = cookieStore.get('type');

    if (user) {
        return NextResponse.json({
            loggedIn: true,
            user: user.value,
            type: type ? type.value : null
        });
    }

    return NextResponse.json({ loggedIn: false });
}
