<style>
    .org-surface {
        position: relative;
        inset: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        overflow: auto;
        background: transparent;
        /* sin gris */
    }

    .org-card {
        width: 100%;
        max-width: 1100px;
        background: transparent;
        border: none;
        box-shadow: none;
        border-radius: 0;
        padding: 0;
        transform: none;
        margin: 0 auto;
    }

    #orgMermaid1>svg {
        display: block;
        margin: 0 auto;
        max-width: 100%;
        height: auto;
    }

    #orgMermaid1 .node rect,
    #orgMermaid1 .node polygon {
        fill: #eef2ff;
        stroke: #6366f1;
        stroke-width: 1.6px;
        rx: 10;
        ry: 10;
        filter: drop-shadow(0 1px 0 rgba(15, 23, 42, .04));
    }

    #orgMermaid1 .label {
        color: #0f172a;
        font-weight: 600;
        font-size: .95rem;
    }

    #orgMermaid1 .edgePath path {
        stroke: #94a3b8;
        stroke-width: 1.6px;
    }

    #orgMermaid1 .arrowheadPath {
        fill: #94a3b8;
    }

    .org-kicker {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: #eef2ff;
        color: #3730a3;
        padding: .25rem .6rem;
        border: 1px solid #c7d2fe;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
    }

    .org-title {
        margin: .5rem 0 0;
        font-weight: 800;
        color: #0f172a;
    }

    .org-subtitle {
        margin-top: .25rem;
        color: #475569;
    }
</style>

<div class="org-surface">
    <div class="org-card">
        <header class="text-center">
            <span class="org-kicker"><i class="fa-solid fa-sitemap"></i> Organigrama</span>
            <h1 class="org-title text-2xl">Estructura organizacional</h1>
            <p class="org-subtitle text-sm">Visualización jerárquica de áreas y dependencias de la empresa.</p>
        </header>

        <div id="orgMermaid1" data-org='@json($datosPrueba ?? [])'></div>

    </div>
</div>
