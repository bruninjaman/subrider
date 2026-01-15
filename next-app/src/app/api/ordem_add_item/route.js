import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function POST(request) {
    try {
        const body = await request.json();
        const { tipo_item, ordem } = body;

        if (!ordem) {
            return NextResponse.json({ error: 'Ordem ID required' }, { status: 400 });
        }

        let sql = '';
        let params = [];

        if (tipo_item === 'pecas') {
            const { pecaid, pquantidade, pvalor, scode } = body;
            const categoria = 2;

            // Fetch Peca details
            const pecaSql = 'SELECT * FROM pecas WHERE pecaId = ?';
            const pecaResult = await query(pecaSql, [pecaid]);

            if (pecaResult.length === 0) {
                return NextResponse.json({ error: 'Peca not found' }, { status: 404 });
            }

            const peca = pecaResult[0];
            const { foto, grupo, item, parte } = peca;
            const tipo = 0;
            const descricao = 0; // Using 0 as per PHP script

            sql = `INSERT INTO item_ordem (Foto, Grupo, Tipo, Item, Parte, Quantidade, Valor, Descricao, Ordem, Categoria, Codigo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`;
            params = [foto, grupo, tipo, item, parte, pquantidade, pvalor, descricao, ordem, categoria, scode];

        } else if (tipo_item === 'service') {
            const { servicoid, squantidade, svalor } = body;
            const categoria = 1;

            // Fetch Service details
            const serviceSql = 'SELECT * FROM servicos WHERE servicoId = ?';
            const serviceResult = await query(serviceSql, [servicoid]);

            if (serviceResult.length === 0) {
                return NextResponse.json({ error: 'Service not found' }, { status: 404 });
            }

            const servico = serviceResult[0];
            const { tipo: prodTipo, item: prodItem } = servico;

            const foto = 0;
            const grupo = 0;
            const parte = 0;
            const descricao = 0;

            sql = `INSERT INTO item_ordem (Foto, Grupo, Tipo, Item, Parte, Quantidade, Valor, Descricao, Ordem, Categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`;
            params = [foto, grupo, prodTipo, prodItem, parte, squantidade, svalor, descricao, ordem, categoria];

        } else if (tipo_item === 'adiantamento') {
            const { avalor, aitem } = body;
            const categoria = 3;

            const foto = 0;
            const grupo = 0;
            const tipo = 0;
            const item = 0;
            const parte = 0;
            const quantidade = 1;
            const descricao = aitem;

            sql = `INSERT INTO item_ordem (Foto, Grupo, Tipo, Item, Parte, Quantidade, Valor, Descricao, Ordem, Categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`;
            params = [foto, grupo, tipo, item, parte, quantidade, avalor, descricao, ordem, categoria];
        } else {
            return NextResponse.json({ error: 'Invalid item type' }, { status: 400 });
        }

        const result = await query(sql, params);
        return NextResponse.json({ success: true, id: result.insertId });

    } catch (error) {
        console.error('Add Item Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}
