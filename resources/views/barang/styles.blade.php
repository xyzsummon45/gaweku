<style>
    :root {
        color-scheme: light;
        font-family: Arial, Helvetica, sans-serif;
        background: #f4f6f8;
        color: #1f2933;
    }

    body {
        margin: 0;
        background: #f4f6f8;
    }

    .top-nav {
        background: #ffffff;
        border-bottom: 1px solid #d9e2ec;
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .top-nav-inner {
        width: min(1120px, calc(100% - 32px));
        margin: 0 auto;
        min-height: 58px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .brand {
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .nav-links a {
        color: #334155;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 700;
        padding: 9px 12px;
        text-decoration: none;
    }

    .nav-links a:hover,
    .nav-links a.active {
        background: #e0f2fe;
        color: #075985;
    }

    .page {
        width: min(1120px, calc(100% - 32px));
        margin: 28px auto 32px;
    }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .page-header p {
        margin: 0 0 6px;
        color: #52606d;
        font-size: 14px;
    }

    h1 {
        margin: 0;
        font-size: 28px;
    }

    .panel {
        background: #ffffff;
        border: 1px solid #d9e2ec;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 18px;
        box-shadow: 0 8px 24px rgba(31, 41, 51, 0.06);
    }

    .toolbar {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .import-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(180px, 1fr)) auto auto;
        gap: 12px;
        align-items: end;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    label span {
        display: block;
        margin-bottom: 6px;
        color: #52606d;
        font-size: 14px;
        font-weight: 700;
    }

    input {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #bcccdc;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 15px;
    }

    small {
        display: block;
        color: #b42318;
        margin-top: 6px;
    }

    .actions {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-top: 18px;
    }

    button,
    .primary-button,
    .secondary-button,
    .danger-button {
        border: 0;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    button,
    .primary-button {
        background: #0f766e;
        color: #ffffff;
    }

    .secondary-button {
        background: #e0f2fe;
        color: #075985;
    }

    .danger-button {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert {
        border-radius: 6px;
        padding: 12px 14px;
        margin-bottom: 16px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 12px;
        border-bottom: 1px solid #d9e2ec;
        text-align: left;
        vertical-align: middle;
    }

    th {
        background: #f8fafc;
        color: #52606d;
        font-size: 13px;
        text-transform: uppercase;
    }

    .number {
        text-align: right;
        white-space: nowrap;
    }

    .row-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .empty {
        text-align: center;
        color: #52606d;
        padding: 24px;
    }

    .pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 16px;
        color: #52606d;
        font-size: 14px;
    }

    .pagination-links {
        display: flex;
        gap: 8px;
    }

    @media (max-width: 720px) {
        .page-header,
        .toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .header-actions {
            justify-content: flex-start;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .filter-form {
            grid-template-columns: 1fr;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .top-nav-inner {
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
            padding: 12px 0;
        }

        .nav-links {
            justify-content: flex-start;
        }
    }
</style>
