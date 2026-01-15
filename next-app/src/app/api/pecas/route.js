import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function GET(request) {
    const { searchParams } = new URL(request.url);
    const id = searchParams.get('id');
    const search = searchParams.get('q') || '';
    const orderby = searchParams.get('orderby') || 'pecaId';
    const order = searchParams.get('order') || 'DESC';
    const page = parseInt(searchParams.get('page')) || 1;
    const limit = parseInt(searchParams.get('limit')) || 5;
    const offset = (page - 1) * limit;

    try {
        if (id) {
            const sql = `SELECT * FROM pecas WHERE pecaId = ?`;
            const results = await query(sql, [id]);
            return NextResponse.json(results[0] || null);
        }

        let sql = `SELECT * FROM pecas`;
        let countSql = `SELECT COUNT(*) as total FROM pecas`;
        const params = [];

        if (search) {
            const whereClause = ` WHERE (item LIKE ? OR grupo LIKE ? OR parte LIKE ?)`;
            sql += whereClause;
            countSql += whereClause;
            const searchPattern = `%${search}%`;
            params.push(searchPattern, searchPattern, searchPattern);
        }

        sql += ` ORDER BY ${orderby} ${order}`;
        sql += ` LIMIT ${limit} OFFSET ${offset}`;

        const results = await query(sql, params);

        // Get total count for pagination
        const countResult = await query(countSql, params);
        const total = countResult[0].total;

        return NextResponse.json({
            items: results,
            total,
            page,
            totalPages: Math.ceil(total / limit)
        });
    } catch (error) {
        console.error('API Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}

export async function DELETE(request) {
    const { searchParams } = new URL(request.url);
    const pecaId = searchParams.get('id');

    if (!pecaId) {
        return NextResponse.json({ error: 'Peca ID required' }, { status: 400 });
    }

    try {
        const result = await query('DELETE FROM pecas WHERE pecaId = ?', [pecaId]);

        if (result.affectedRows === 0) {
            return NextResponse.json({ error: 'Peça não encontrada ou já excluída.' }, { status: 404 });
        }

        return NextResponse.json({ success: true });
    } catch (error) {
        console.error('Delete Error:', error);
        return NextResponse.json({ error: 'Erro ao excluir peça.' }, { status: 500 });
    }
}

export async function POST(request) {
    try {
        const body = await request.json();
        const { grupo, item, parte, foto } = body;

        if (!item || !grupo) {
            return NextResponse.json({ error: 'Missing required fields' }, { status: 400 });
        }

        const result = await query(
            'INSERT INTO pecas (grupo, item, parte, foto) VALUES (?, ?, ?, ?)',
            [grupo, item, parte, foto]
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
        const { pecaId, grupo, item, parte, foto } = body;

        if (!pecaId || !item || !grupo) {
            return NextResponse.json({ error: 'Missing required fields' }, { status: 400 });
        }

        await query(
            'UPDATE pecas SET grupo = ?, item = ?, parte = ?, foto = ? WHERE pecaId = ?',
            [grupo, item, parte, foto, pecaId]
        );

        return NextResponse.json({ success: true });
    } catch (error) {
        console.error('Update Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}
