import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function GET(request) {
    const { searchParams } = new URL(request.url);
    const id = searchParams.get('id');
    const search = searchParams.get('q') || '';
    const orderby = searchParams.get('orderby') || 'servicoId';
    const order = searchParams.get('order') || 'DESC';

    try {
        let sql = `SELECT * FROM servicos`;
        const params = [];

        if (id) {
            sql += ` WHERE servicoId = ?`;
            params.push(id);
            const results = await query(sql, params);
            return NextResponse.json(results[0] || null);
        }

        if (search) {
            sql += ` WHERE (item LIKE ? OR tipo LIKE ?)`;
            const searchPattern = `%${search}%`;
            params.push(searchPattern, searchPattern);
        }

        sql += ` ORDER BY ${orderby} ${order}`;

        const results = await query(sql, params);
        return NextResponse.json(results);
    } catch (error) {
        console.error('API Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}

export async function DELETE(request) {
    const { searchParams } = new URL(request.url);
    const servicoId = searchParams.get('id');

    if (!servicoId) {
        return NextResponse.json({ error: 'Service ID required' }, { status: 400 });
    }

    try {
        const result = await query('DELETE FROM servicos WHERE servicoId = ?', [servicoId]);

        if (result.affectedRows === 0) {
            return NextResponse.json({ error: 'Serviço não encontrado ou já excluído.' }, { status: 404 });
        }

        return NextResponse.json({ success: true });
    } catch (error) {
        console.error('Delete Error:', error);

        // Check for specific MySQL foreign key constraint error
        if (error.code === 'ER_ROW_IS_REFERENCED_2' || error.errno === 1451) {
            return NextResponse.json({ error: 'Este serviço está vinculado a uma ou mais ordens e não pode ser excluído.' }, { status: 409 });
        }

        return NextResponse.json({ error: 'Erro no banco de dados ao tentar excluir.' }, { status: 500 });
    }
}

export async function POST(request) {
    try {
        const body = await request.json();
        const { item, tipo } = body;

        if (!item || !tipo) {
            return NextResponse.json({ error: 'Missing required fields' }, { status: 400 });
        }

        const result = await query(
            'INSERT INTO servicos (item, tipo) VALUES (?, ?)',
            [item, tipo]
        );

        return NextResponse.json({ success: true, id: result.insertId });
    } catch (error) {
        console.error('Create Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}

export async function PUT(request) {
    try {
        const body = await request.json();
        const { servicoId, item, tipo } = body;

        if (!servicoId || !item || !tipo) {
            return NextResponse.json({ error: 'Missing required fields' }, { status: 400 });
        }

        await query(
            'UPDATE servicos SET item = ?, tipo = ? WHERE servicoId = ?',
            [item, tipo, servicoId]
        );

        return NextResponse.json({ success: true });
    } catch (error) {
        console.error('Update Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}

