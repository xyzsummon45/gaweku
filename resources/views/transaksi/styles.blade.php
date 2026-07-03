@include('barang.styles')

<style>
    .cashier-panel {
        display: grid;
        grid-template-columns: minmax(240px, 1fr) 120px auto;
        gap: 12px;
        align-items: end;
    }

    .item-search {
        position: relative;
    }

    .suggestions {
        position: absolute;
        z-index: 20;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        background: #ffffff;
        border: 1px solid #bcccdc;
        border-radius: 8px;
        box-shadow: 0 12px 28px rgba(31, 41, 51, 0.14);
        overflow: hidden;
    }

    .suggestion-item {
        width: 100%;
        border-radius: 0;
        background: #ffffff;
        color: #1f2933;
        display: block;
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #edf2f7;
    }

    .suggestion-item:hover {
        background: #f0fdfa;
    }

    .suggestion-item strong,
    .suggestion-item span {
        display: block;
    }

    .suggestion-item span {
        margin-top: 4px;
        color: #52606d;
        font-size: 13px;
        font-weight: 400;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .summary-grid span {
        color: #52606d;
        display: block;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .summary-grid strong {
        font-size: 18px;
    }

    tfoot th {
        border-bottom: 0;
        background: #ffffff;
        font-size: 16px;
    }

    @media (max-width: 720px) {
        .cashier-panel {
            grid-template-columns: 1fr;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
