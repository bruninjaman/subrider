import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function DELETE(request, { params }) {
    const { itemId } = await params;
    try {
        const result = await query(
            'DELETE FROM item_ordem WHERE item_ordemID = ?',
            [itemId]
        );
        return NextResponse.json({ success: true, result });
    } catch (error) {
        console.error('Delete error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}
