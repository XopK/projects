<footer class="footer sm:footer-horizontal bg-base-200 text-base-content p-10 pb-20">
    <aside>
        <a href="{{route('index')}}" class="text-2xl font-bold">Все танцы</a>
        <p>
            ИП: Пудикова Залия Мунировна
            <br/>
            ИНН: 027407674680
            <br/>
            ОГРНИП: 322028000160589
        </p>
    </aside>
    <nav>
        <h6 class="footer-title">Основное</h6>
        <a href="{{route('index')}}" class="link link-hover">Главная</a>
        <a href="{{route('groups')}}" class="link link-hover">Поиск</a>
        <a href="{{route('teachers')}}" class="link link-hover">Преподаватели</a>
        @auth
            <a href="{{route('profile')}}" class="link link-hover">Личный профиль</a>
        @endauth
    </nav>
    <nav>
        <h6 class="footer-title">Company</h6>
        <a class="link link-hover">About us</a>
        <a class="link link-hover">Contact</a>
        <a class="link link-hover">Jobs</a>
        <a class="link link-hover">Press kit</a>
    </nav>
    <nav>
        <h6 class="footer-title">Legal</h6>
        <a class="link link-hover">Terms of use</a>
        <a class="link link-hover">Privacy policy</a>
        <a class="link link-hover">Cookie policy</a>
    </nav>
</footer>
