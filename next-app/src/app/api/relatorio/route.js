import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function GET(request) {
    const { searchParams } = new URL(request.url);
    const ordem_id = searchParams.get('ordem');

    if (!ordem_id) {
        return NextResponse.json({ status: 'error', message: 'ID da ordem não especificado' }, { status: 400 });
    }

    try {
        // Verificar se a ordem existe
        const checkOrdem = await query('SELECT Codigo FROM ordem_servicos WHERE Codigo = ?', [ordem_id]);
        if (checkOrdem.length === 0) {
            return NextResponse.json({ status: 'error', message: 'Ordem de serviço não encontrada' }, { status: 404 });
        }

        // Buscar relatório
        const results = await query('SELECT * FROM relatorios WHERE ordem_id = ?', [ordem_id]);

        if (results.length > 0) {
            const relatorio = results[0];
            return NextResponse.json({
                status: 'success',
                conteudo: relatorio.conteudo,
                data_conclusao: relatorio.data_conclusao,
                observacoes_finais: relatorio.observacoes_finais,
                quilometragem: relatorio.quilometragem,
                data_criacao: relatorio.data_criacao,
                data_modificacao: relatorio.data_modificacao
            });
        } else {
            return NextResponse.json({
                status: 'novo',
                message: 'Relatório não encontrado'
            });
        }
    } catch (error) {
        console.error('API Error:', error);
        return NextResponse.json({ status: 'error', message: error.message }, { status: 500 });
    }
}

export async function POST(request) {
    const { searchParams } = new URL(request.url);
    const ordem_id = searchParams.get('ordem');

    if (!ordem_id) {
        return NextResponse.json({ status: 'error', message: 'ID da ordem não especificado' }, { status: 400 });
    }

    try {
        const formData = await request.formData();
        const conteudo = formData.get('conteudo');
        const data_conclusao = formData.get('data_conclusao');
        const observacoes_finais = formData.get('observacoes_finais');
        const quilometragem = formData.get('quilometragem');

        // Verificar se a ordem existe
        const checkOrdem = await query('SELECT Codigo FROM ordem_servicos WHERE Codigo = ?', [ordem_id]);
        if (checkOrdem.length === 0) {
            return NextResponse.json({ status: 'error', message: 'Ordem de serviço não encontrada' }, { status: 404 });
        }

        // Verificar se já existe um relatório
        const results = await query('SELECT * FROM relatorios WHERE ordem_id = ?', [ordem_id]);

        if (results.length > 0) {
            // Atualizar
            await query(
                'UPDATE relatorios SET conteudo = ?, data_conclusao = ?, observacoes_finais = ?, quilometragem = ?, data_modificacao = NOW() WHERE ordem_id = ?',
                [conteudo, data_conclusao, observacoes_finais, quilometragem, ordem_id]
            );
            return NextResponse.json({ status: 'success', message: 'Relatório atualizado com sucesso!' });
        } else {
            // Inserir
            await query(
                'INSERT INTO relatorios (ordem_id, conteudo, data_conclusao, observacoes_finais, quilometragem, data_criacao, data_modificacao) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
                [ordem_id, conteudo, data_conclusao, observacoes_finais, quilometragem]
            );
            return NextResponse.json({ status: 'success', message: 'Relatório salvo com sucesso!' });
        }
    } catch (error) {
        console.error('API Error:', error);
        return NextResponse.json({ status: 'error', message: error.message }, { status: 500 });
    }
}
