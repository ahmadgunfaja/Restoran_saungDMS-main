<x-guest-layout>
    section id="detail" style="margin-top: 100px;" class="py-5">
    <div class="container col-xxl-8">

        <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary">
            <div class="container-fluid">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="/">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="/berita">Berita</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="/detail/{{ $artikel->slug }}">{{ $artikel->judul }}</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </nav>

        <img src="{{ asset('storage/artikel/' . $artikel->image) }}" class="image-fluid py-3" style="display:block; margin:auto;" width="500px" height="100%" alt="">
        <div class="konten-berita">
            <p class="mb-3 text-secondary">{{ $artikel->created_at->format('d M Y') }}</p> <!-- Corrected field name -->
            <h4 class="fw-bold mb-3">{{ $artikel->judul }}</h4>
            <p class="text-secondary">{!! $artikel->desc !!}</p>
        </div>
    </div>
</section>
</x-guest-layout>
