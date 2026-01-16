import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

// GET - Fetch item data for editing
export async function GET(request) {
    const { searchParams } = new URL(request.url);
    const itemOrdemId = searchParams.get('item_ordemID');

    if (!itemOrdemId) {
        return NextResponse.json({ error: 'item_ordemID is required' }, { status: 400 });
    }

    try {
        const items = await query(
            'SELECT * FROM item_ordem WHERE item_ordemID = ?',
            [itemOrdemId]
        );

        if (items.length === 0) {
            return NextResponse.json({ error: 'Item not found' }, { status: 404 });
        }

        return NextResponse.json({ item: items[0] });
    } catch (error) {
        console.error('Database error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}

// PUT - Update item
export async function PUT(request) {
    try {
        const body = await request.json();
        const { tipo_item, ordem, item_ordemID } = body;

        if (!tipo_item || !ordem || !item_ordemID) {
            return NextResponse.json({ error: 'Missing required fields' }, { status: 400 });
        }

        let foto = '0';
        let grupo = '0';
        let tipo = '0';
        let item = '0';
        let parte = '0';
        let quantidade = 1;
        let valor = 0;
        let descricao = '0';
        let categoria = 1;
        let codigo = '';

        switch (tipo_item) {
            case 'pecas':
                categoria = 2;
                // Fetch part data
                const partResult = await query(
                    'SELECT * FROM pecas WHERE pecaId = ?',
                    [body.pecaid]
                );
                if (partResult.length > 0) {
                    const peca = partResult[0];
                    foto = peca.foto || '0';
                    grupo = peca.grupo || '0';
                    item = peca.item || '0';
                    parte = peca.parte || '0';
                    quantidade = body.pquantidade || 1;
                    valor = body.pvalor || 0;
                    codigo = body.scode || '';
                }
                break;

            case 'service':
                categoria = 1;
                // Fetch service data
                const serviceResult = await query(
                    'SELECT * FROM servicos WHERE servicoId = ?',
                    [body.servicoid]
                );
                if (serviceResult.length > 0) {
                    const servico = serviceResult[0];
                    tipo = servico.tipo || '0';
                    item = servico.item || '0';
                    quantidade = body.squantidade || 1;
                    valor = body.svalor || 0;
                }
                break;

            case 'adiantamento':
                categoria = 3;
                quantidade = 1;
                valor = body.avalor || 0;
                descricao = body.aitem || '0';
                break;

            default:
                return NextResponse.json({ error: 'Invalid tipo_item' }, { status: 400 });
        }

        // Update the item
        const updateResult = await query(
            `UPDATE item_ordem SET 
                Foto = ?, 
                Grupo = ?, 
                Tipo = ?, 
                Item = ?, 
                Parte = ?, 
                Quantidade = ?, 
                Valor = ?, 
                Descricao = ?, 
                Ordem = ?, 
                Categoria = ?,
                Codigo = ?
            WHERE item_ordemID = ?`,
            [foto, grupo, tipo, item, parte, quantidade, valor, descricao, ordem, categoria, codigo, item_ordemID]
        );

        return NextResponse.json({ success: true, result: updateResult });
    } catch (error) {
        console.error('Error updating item:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}
