import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function GET(request, { params }) {
    const rawParams = await params;
    const id = Array.isArray(rawParams.id) ? rawParams.id.join('/') : rawParams.id;

    try {
        // 1. Fetch Order Info
        const orders = await query(
            'SELECT * FROM ordem_servicos WHERE Codigo = ?',
            [id]
        );
        if (!orders || orders.length === 0) {
            return NextResponse.json({ error: 'Order not found' }, { status: 404 });
        }
        const order = orders[0];
        let owner = order.proprietario_ordem;

        // 2. Fetch Motorcycle Info
        const motorcycles = await query(
            'SELECT * FROM motocicletas WHERE motoId = ?',
            [order.motoID]
        );
        const motorcycle = motorcycles[0] || null;

        // PHP logic: if owner_ordem is null, use motorcycle owner and update
        if (!owner && motorcycle) {
            owner = motorcycle.proprietario;
            await query(
                'UPDATE ordem_servicos SET proprietario_ordem = ? WHERE Codigo = ?',
                [owner, id]
            );
            order.proprietario_ordem = owner;
        }

        // 3. Fetch Items (Services, Parts, Payments)
        const items = await query(
            'SELECT * FROM item_ordem WHERE Ordem = ?',
            [id]
        );

        // 4. Fetch Measurements from all component tables
        const componentTables = ['bomba', 'embreagem', 'motor', 'virabrequim', 'cabecote'];
        const measurements = {};
        const references = {};

        for (const table of componentTables) {
            // Measurements (is_reference = 0)
            const tableData = await query(
                `SELECT * FROM ${table} WHERE ordem = ? AND is_reference = 0`,
                [id]
            );
            measurements[table] = tableData[0] || null;

            // References (is_reference = 1)
            const refData = await query(
                `SELECT * FROM ${table} WHERE ordem = ? AND is_reference = 1`,
                [id]
            );
            references[table] = refData[0] || null;
        }

        return NextResponse.json({
            order,
            motorcycle,
            items,
            measurements,
            references
        });
    } catch (error) {
        console.error('Database error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}

export async function PATCH(request, { params }) {
    const rawParams = await params;
    const id = Array.isArray(rawParams.id) ? rawParams.id.join('/') : rawParams.id;
    try {
        const body = await request.json();
        const { date, owner, km } = body;

        const updates = [];
        const values = [];

        if (date !== undefined) {
            updates.push('Data = ?');
            values.push(date);
        }
        if (owner !== undefined) {
            updates.push('proprietario_ordem = ?');
            values.push(owner);
        }
        if (km !== undefined) {
            updates.push('KM = ?');
            values.push(km);
        }

        if (updates.length === 0) {
            return NextResponse.json({ error: 'No fields to update' }, { status: 400 });
        }

        values.push(id);
        const result = await query(
            `UPDATE ordem_servicos SET ${updates.join(', ')} WHERE Codigo = ?`,
            values
        );

        return NextResponse.json({ success: true, result });
    } catch (error) {
        console.error('Update error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}
