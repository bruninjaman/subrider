export function formatNumber(value, decimals = 3) {
    if (value === null || value === '' || value == 0) return null;
    if (isNaN(value)) return value;
    return new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(value);
}

export function formatCurrency(value) {
    if (value === null || value === '' || isNaN(value)) return 'R$ 0,00';
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
}

export function formatDate(dateString) {
    if (!dateString) return 'dd/mm/aaaa';
    try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('pt-BR').format(date);
    } catch (e) {
        return dateString;
    }
}

export function isInRange(value, reference, validationType) {
    if (value === null || value === '' || reference === null || reference === '' || reference == 0)
        return true;

    const v = parseFloat(value);
    const r = parseFloat(reference);

    if (isNaN(v) || isNaN(r)) return true;

    switch (validationType) {
        case 'min':
            return v >= r;
        case 'max':
            return v <= r;
        case 'exact':
            // 5% tolerance
            const tolerance = r * 0.05;
            return Math.abs(v - r) <= tolerance;
        default:
            return true;
    }
}

export function getValidationType(campo, table) {
    const validationRules = {
        bomba: {
            pressao_oleo_min: 'min',
            pressao_oleo_max: 'max',
            vazao_min: 'min',
            vazao_max: 'max',
            comb_pressao: 'exact'
        },
        embreagem: {
            disco_friccao_espes: 'min',
            disco_separador_emp: 'max'
        },
        motor: {
            diametro_cilindro_max: 'max',
            conicidade_max: 'max',
            ovalizacao_max: 'max',
            diametro_pistao_min: 'min',
            folga_cil_pis_max: 'max',
            aber_anel_1_max: 'max',
            aber_anel_2_max: 'max',
            aber_anel_1_pres_min: 'min',
            aber_anel_2_pres_min: 'min',
            larg_anel_1_min: 'min',
            larg_anel_2_min: 'min',
            dia_furo_pis_min: 'min',
            dia_pino_pis_min: 'min',
            folga_pino_pis_max: 'max'
        },
        virabrequim: {
            folga_mancal: 'exact',
            folga_biela: 'exact',
            folga_bronzina: 'exact',
            folga_bronzinha: 'exact',
            folga_lateral_biela: 'exact',
            folga_lateral_eixo_min: 'min',
            folga_lateral_eixo_max: 'max',
            empenamento: 'max',
            diametro_moente: 'exact',
            diametro_munhao: 'exact'
        },
        cabecote: {
            val_adm_limite_min: 'min',
            val_adm_limite_max: 'max',
            val_esc_limite_min: 'min',
            val_esc_limite_max: 'max'
        }
    };

    return validationRules[table]?.[campo] || 'exact';
}
