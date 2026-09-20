@extends('layouts.app')

@section('content')
    <div class="page-shell" data-help-center>
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">menu_book</span> Product guide</span>
                <h2>Find your way around RBAC Payroll</h2>
                <p>Follow verified setup and daily-use workflows, or search this guide for a specific task.</p>
            </div>
            <div class="hero-actions">
                <a class="btn btn-ghost" href="{{ route('chatbot.authenticated') }}" onclick="event.preventDefault(); document.querySelector('[data-chatbot-toggle]').click();">
                    <span class="material-symbols-rounded">support_agent</span> Ask support
                </a>
            </div>
        </section>

        <section class="card help-guide-shell">
            <label class="help-search" for="help-search-input">
                <span class="material-symbols-rounded">search</span>
                <input id="help-search-input" type="search" placeholder="Search registration, employees, leave, payslips..." data-help-search>
            </label>
            <p class="help-search-status" data-help-search-status></p>
            <article class="help-guide prose" data-help-content>
                {!! $guideHtml !!}
            </article>
        </section>
    </div>

    <style>
        .help-guide-shell { display: grid; gap: 1rem; }
        .help-search { display: flex; align-items: center; gap: .65rem; border: 1px solid rgba(148,163,184,.2); border-radius: 1rem; background: rgba(2,6,23,.35); padding: .75rem 1rem; color: #94a3b8; }
        .help-search input { width: 100%; border: 0; outline: 0; background: transparent; color: #e2e8f0; font: inherit; }
        .help-search-status { min-height: 1rem; margin: 0; color: #94a3b8; font-size: .8rem; }
        .help-guide { color: #cbd5e1; line-height: 1.7; }
        .help-guide h1 { margin: 0 0 1rem; color: #f8fafc; font-size: 1.7rem; line-height: 1.2; }
        .help-guide h2 { margin: 2rem 0 .65rem; color: #f8fafc; font-size: 1.2rem; }
        .help-guide h3 { margin: 1.35rem 0 .5rem; color: #e2e8f0; font-size: 1rem; }
        .help-guide p, .help-guide ul, .help-guide ol { margin: .65rem 0; }
        .help-guide li { margin: .35rem 0; padding-left: .2rem; }
        .help-guide strong { color: #f8fafc; }
        .help-guide blockquote { margin: 1rem 0; border-left: 3px solid #2dd4bf; border-radius: .5rem; background: rgba(45,212,191,.08); padding: .75rem 1rem; color: #ccfbf1; }
        .help-guide a { color: #67e8f9; text-decoration: underline; }
        .help-guide code { border-radius: .35rem; background: rgba(255,255,255,.08); padding: .1rem .3rem; color: #a7f3d0; }
        .help-guide hr { margin: 2rem 0; border: 0; border-top: 1px solid rgba(148,163,184,.18); }
    </style>

    <script>
        (() => {
            const input = document.querySelector('[data-help-search]');
            const content = document.querySelector('[data-help-content]');
            const status = document.querySelector('[data-help-search-status]');
            if (!input || !content || !status) return;

            const sections = [...content.querySelectorAll('h2, h3')];
            input.addEventListener('input', () => {
                const query = input.value.trim().toLowerCase();
                let visible = 0;

                sections.forEach((heading) => {
                    let matches = heading.textContent.toLowerCase().includes(query);
                    let sibling = heading.nextElementSibling;
                    const group = [heading];

                    while (sibling && !['H2', 'H3'].includes(sibling.tagName)) {
                        group.push(sibling);
                        if (sibling.textContent.toLowerCase().includes(query)) matches = true;
                        sibling = sibling.nextElementSibling;
                    }

                    group.forEach((element) => { element.hidden = Boolean(query) && !matches; });
                    if (!query || matches) visible++;
                });

                status.textContent = query ? `${visible} matching sections` : '';
            });
        })();
    </script>
@endsection
