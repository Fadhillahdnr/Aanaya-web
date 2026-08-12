<footer class="user-footer" data-footer-experience aria-labelledby="footer-closing-title">
    <div class="user-footer__bridge" aria-hidden="true"></div>
    <div class="user-footer__grain" aria-hidden="true"></div>

    <div class="user-footer__inner">
        <section class="user-footer__closing" data-footer-reveal>
            <p class="user-footer__eyebrow">The final chord</p>
            <h2 id="footer-closing-title">Keep<br><em>dreaming.</em></h2>
            <p class="user-footer__thought">The song may end. The feeling does not.</p>
        </section>

        <div class="user-footer__navigation">
            <nav class="user-footer__explore" aria-label="Explore Aanaya" data-footer-reveal>
                <p class="user-footer__label">Explore</p>
                <a href="{{ route('dashboard') }}" @class(['is-current' => request()->routeIs('dashboard')])>Dashboard <span aria-hidden="true">↗</span></a>
                <a href="{{ route('music') }}" @class(['is-current' => request()->routeIs('music')])>Music <span aria-hidden="true">↗</span></a>
                <a href="{{ route('articles') }}" @class(['is-current' => request()->routeIs('articles*')])>Stories <span aria-hidden="true">↗</span></a>
                <a href="{{ route('gallery') }}" @class(['is-current' => request()->routeIs('gallery')])>Gallery <span aria-hidden="true">↗</span></a>
                <a href="{{ route('merchandise') }}" @class(['is-current' => request()->routeIs('merchandise*')])>Store <span aria-hidden="true">↗</span></a>
                <a href="{{ route('about') }}" @class(['is-current' => request()->routeIs('about')])>About <span aria-hidden="true">↗</span></a>
            </nav>

            <nav class="user-footer__socials" aria-label="Aanaya social media" data-footer-reveal>
                <p class="user-footer__label">Connect</p>
                @php
                    $socialLinks = [
                        ['YouTube', 'fab fa-youtube', 'https://www.youtube.com/@aanaya.u'],
                        ['Spotify', 'fab fa-spotify', 'https://open.spotify.com/artist/2oIl3sAETcKUSCsMYw39eL?si=tZ9dJ_nJSRW30yMIobpMLg'],
                        ['WhatsApp', 'fab fa-whatsapp', 'https://wa.me/6289648138321'],
                        ['TikTok', 'fab fa-tiktok', 'https://tiktok.com/@aanaya.u?is_from_webapp=1&sender_device=pc'],
                        ['Instagram', 'fab fa-instagram', 'https://instagram.com/aanaya.u?utm_source=ig_web_button_share_sheet&igsh=ZD'],
                    ];
                @endphp

                @foreach ($socialLinks as [$label, $icon, $url])
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="user-footer__social">
                        <span class="user-footer__social-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <i class="{{ $icon }}" aria-hidden="true"></i>
                        <strong>{{ $label }}</strong>
                        <span class="user-footer__arrow" aria-hidden="true">↗</span>
                        <span class="sr-only">Aanaya on {{ $label }}, opens in a new tab</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <p class="user-footer__wordmark" aria-hidden="true" data-footer-wordmark>AANAYA</p>

        <div class="user-footer__bottom" data-footer-reveal>
            <p>© {{ date('Y') }} Aanaya <span>—</span> Made from music, stories, and feelings.</p>
            <a href="#page-top" class="user-footer__top">Back to top <span aria-hidden="true">↑</span></a>
        </div>
    </div>
</footer>
