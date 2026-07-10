/**
 * PAY by square QR generation for invoice preview.
 */
window.InvoicePayBySquare = {
    async encode(params) {
        const { encode } = await import('https://esm.sh/bysquare@4.0.0/pay');

        const iban = (params.iban || '').replace(/\s+/g, '').toUpperCase();
        const amount = Number(params.amount);

        return encode({
            payments: [
                {
                    type: 1,
                    amount: amount > 0 ? amount : undefined,
                    currencyCode: params.currency || 'EUR',
                    variableSymbol: String(params.variableSymbol || '').replace(/\D/g, '').slice(0, 10) || undefined,
                    paymentNote: params.note || undefined,
                    bankAccounts: [
                        {
                            iban,
                            bic: params.swift || undefined,
                        },
                    ],
                    beneficiary: {
                        name: params.beneficiaryName || 'Platba',
                    },
                },
            ],
        });
    },

    async renderToCanvas(canvas, params) {
        const QRCode = (await import('https://esm.sh/qrcode@1.5.4')).default;
        const payload = await this.encode(params);

        await QRCode.toCanvas(canvas, payload, {
            width: 140,
            margin: 1,
            color: { dark: '#0c7a61', light: '#ffffff' },
        });
    },

    async qrImageUrl(params) {
        const QRCode = (await import('https://esm.sh/qrcode@1.5.4')).default;
        const payload = await this.encode(params);

        return QRCode.toDataURL(payload, {
            width: 140,
            margin: 1,
            color: { dark: '#0c7a61', light: '#ffffff' },
        });
    },
};
