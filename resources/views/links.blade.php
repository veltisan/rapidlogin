<div id="rapidloginLinks" style="display: flex; position: fixed; top: 0; right: 0; left: 0; gap: 0.5rem; margin: auto; margin-top: 0.5rem; width: fit-content;z-index: 9999">
    @foreach($users as $userId => $userName)
        <a
            href="{{ route('rapidlogin.login', ['user' => $userId, 'guard' => $guard]) }}"
            style="display: inline-block; padding: 0.25rem 0.5rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem; color: #fff; background-color: #6c757d; text-align: center; vertical-align: middle; user-select: none; font-weight: 400; text-decoration: none;"
        >
            {{ $userName }}
        </a>
    @endforeach

    @if($showCloseButton)
        <a
            type="button"
            style="display: inline-block; padding: 0.25rem 0.5rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem; color: #fff; background-color: #dc3545; text-align: center; vertical-align: middle; user-select: none; font-weight: 400; text-decoration: none;"
            onclick="document.getElementById('rapidloginLinks').remove();"
        >
            X
        </a>
    @endif
    @foreach($links as $text => $url)
        <a
            href="{{ $url }}"
            target="_blank"
            style="display: inline-block; padding: 0.25rem 0.5rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem; color: #fff; background-color: #5aafba; text-align: center; vertical-align: middle; user-select: none; font-weight: 400; text-decoration: none;"
        >
            {{ $text }}
        </a>
    @endforeach
</div>
