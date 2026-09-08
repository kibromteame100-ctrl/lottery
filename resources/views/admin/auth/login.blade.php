<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Lottery Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        [x-cloak]{display:none!important}
        html,body{height:100%;font-family:'Inter',sans-serif;-webkit-font-smoothing:antialiased}

        body{
            min-height:100vh;
            background:#13112b;
        }

        /* ── Background scene ─────────────────────── */
        .bg{
            position:fixed;inset:0;z-index:0;overflow:hidden;
        }
        .bg-grad{
            position:absolute;inset:0;
            background:
                radial-gradient(ellipse 72% 62% at 18% 54%, rgba(76,50,195,.65) 0%, transparent 62%),
                radial-gradient(ellipse 52% 68% at 82% 18%, rgba(46,34,160,.48) 0%, transparent 58%),
                radial-gradient(ellipse 48% 44% at 62% 88%, rgba(96,48,200,.32) 0%, transparent 54%),
                linear-gradient(158deg,#1c1a3e 0%,#14122e 55%,#0f0e24 100%);
        }
        .bg-grid{
            position:absolute;inset:0;
            background-image:
                linear-gradient(rgba(100,80,230,.04) 1px,transparent 1px),
                linear-gradient(90deg,rgba(100,80,230,.04) 1px,transparent 1px);
            background-size:58px 58px;
        }

        /* orb */
        @keyframes orb{
            0%,100%{transform:translate(0,0) scale(1);}
            40%{transform:translate(14px,-18px) scale(1.07);}
            70%{transform:translate(-10px,11px) scale(.94);}
        }
        .orb{
            position:absolute;border-radius:50%;
            filter:blur(85px);pointer-events:none;
            animation:orb 13s ease-in-out infinite;
        }

        /* rings */
        .ring{
            position:absolute;border-radius:50%;
            border:1px solid rgba(108,85,225,.10);
            pointer-events:none;
        }

        /* glow dot */
        .gdot{position:absolute;border-radius:50%;pointer-events:none;}

        /* ── Brand icon box ───────────────────────── */
        .ibox{
            border-radius:18px;
            background:linear-gradient(145deg,#8b7ee8 0%,#6655d4 45%,#4d40c0 100%);
            box-shadow:
                0 10px 36px rgba(108,90,220,.58),
                inset 0 1px 0 rgba(255,255,255,.12),
                0 0 70px rgba(108,90,220,.18);
            display:flex;align-items:center;justify-content:center;
        }

        /* ── Feature rows ─────────────────────────── */
        .feat{display:flex;align-items:center;gap:13px;}
        .ficon{
            width:38px;height:38px;border-radius:11px;
            display:flex;align-items:center;justify-content:center;
            font-size:16px;flex-shrink:0;
        }

        /* ── Login card ───────────────────────────── */
        .card{
            background:linear-gradient(158deg,rgba(32,29,65,.94) 0%,rgba(24,22,53,.97) 100%);
            border-radius:22px;
            border:1px solid rgba(255,255,255,.07);
            box-shadow:
                0 0 0 1px rgba(108,90,220,.13),
                0 32px 80px rgba(0,0,0,.60),
                0 0 100px rgba(78,56,200,.12),
                inset 0 1px 0 rgba(255,255,255,.06);
        }

        /* ── Input field ──────────────────────────── */
        .field{
            display:flex;align-items:center;gap:11px;
            background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.08);
            border-radius:11px;
            padding:0 14px;height:50px;
            transition:border-color .2s,background .2s,box-shadow .2s;
        }
        .field:focus-within{
            border-color:rgba(120,108,234,.65);
            background:rgba(108,90,220,.08);
            box-shadow:0 0 0 3px rgba(108,90,220,.14);
        }
        .field input{
            flex:1;min-width:0;
            background:transparent;border:none;outline:none;
            color:rgba(255,255,255,.88);
            font-family:'Inter',sans-serif;font-size:14px;
        }
        .field input::placeholder{color:rgba(255,255,255,.20);}
        .field svg{flex-shrink:0;color:rgba(255,255,255,.25);}

        /* ── Sign In button ───────────────────────── */
        .btn{
            width:100%;height:50px;
            display:flex;align-items:center;justify-content:center;gap:9px;
            border-radius:11px;border:none;cursor:pointer;
            font-family:'Inter',sans-serif;font-size:15px;font-weight:700;color:#fff;
            background:linear-gradient(135deg,#7c6fea 0%,#5c50d0 55%,#4840be 100%);
            box-shadow:0 5px 24px rgba(108,95,230,.52),inset 0 1px 0 rgba(255,255,255,.14);
            position:relative;overflow:hidden;
            transition:transform .18s,box-shadow .18s,background .18s;
        }
        .btn::after{
            content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(255,255,255,.10) 0%,transparent 55%);
            pointer-events:none;
        }
        .btn:hover{
            background:linear-gradient(135deg,#9180f0 0%,#6d61dc 55%,#5850cc 100%);
            box-shadow:0 8px 36px rgba(108,95,230,.68),inset 0 1px 0 rgba(255,255,255,.16);
            transform:translateY(-1px);
        }
        .btn:active{transform:translateY(0);}

        /* ── Checkbox ─────────────────────────────── */
        input[type=checkbox]{
            appearance:none;-webkit-appearance:none;
            width:16px;height:16px;border-radius:5px;
            border:1.5px solid rgba(255,255,255,.22);
            background:rgba(255,255,255,.05);
            cursor:pointer;transition:all .18s;
            position:relative;flex-shrink:0;margin:0;
        }
        input[type=checkbox]:checked{
            background:linear-gradient(135deg,#7c6fea,#5248c8);
            border-color:#7c6fea;
            box-shadow:0 0 10px rgba(124,111,234,.4);
        }
        input[type=checkbox]:checked::after{
            content:'';position:absolute;
            left:4px;top:1px;width:5px;height:9px;
            border:2px solid #fff;
            border-top:none;border-left:none;
            transform:rotate(45deg);
        }
    </style>
</head>
<body>

{{-- Background --}}
<div class="bg">
    <div class="bg-grad"></div>
    <div class="bg-grid"></div>

    <div class="orb" style="width:min(620px,75vw);height:min(620px,75vw);
         background:radial-gradient(circle,rgba(80,50,198,.60),rgba(40,24,138,.28));
         top:-18%;left:-10%;opacity:.55;animation-delay:0s;"></div>
    <div class="orb" style="width:min(460px,58vw);height:min(460px,58vw);
         background:radial-gradient(circle,rgba(44,32,162,.58),rgba(18,12,98,.26));
         bottom:-16%;right:-8%;opacity:.50;animation-delay:-5.5s;"></div>
    <div class="orb" style="width:min(280px,32vw);height:min(280px,32vw);
         background:radial-gradient(circle,rgba(102,50,210,.52),rgba(58,22,155,.26));
         top:32%;right:10%;opacity:.32;animation-delay:-10s;"></div>

    <div class="ring" style="width:440px;height:440px;bottom:-148px;left:-148px;"></div>
    <div class="ring" style="width:680px;height:680px;bottom:-240px;left:-240px;opacity:.55;"></div>
    <div class="ring" style="width:240px;height:240px;top:8px;right:8px;"></div>
    <div class="ring" style="width:370px;height:370px;top:-62px;right:-62px;opacity:.55;"></div>

    <div class="gdot" style="width:8px;height:8px;top:27%;left:30%;
         background:#7c6fea;opacity:.60;box-shadow:0 0 16px 5px rgba(124,111,234,.38);"></div>
    <div class="gdot" style="width:9px;height:9px;top:60%;right:24%;
         background:#5c4fd4;opacity:.50;box-shadow:0 0 14px 4px rgba(92,79,212,.32);"></div>
    <div class="gdot" style="width:5px;height:5px;bottom:24%;left:50%;
         background:#a899f5;opacity:.38;"></div>
</div>

{{-- ═══════════════════════════════════════════════════
     LAYOUT
═══════════════════════════════════════════════════ --}}
<div style="position:relative;z-index:10;min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            padding:2rem 1.25rem;">

    {{-- Two-column on md+, single column on mobile --}}
    <div style="display:flex;align-items:center;justify-content:center;
                gap:clamp(2.5rem,5vw,4.5rem);
                width:100%;max-width:940px;">

        {{-- ══════════════════════════
             LEFT — hidden on mobile
        ══════════════════════════ --}}
        <div style="flex:1;min-width:0;display:none;" class="left-panel">
            {{-- Brand icon --}}
            <div class="ibox" style="width:66px;height:66px;margin-bottom:1.6rem;">
                <span style="font-size:28px;">🎰</span>
            </div>

            {{-- Name --}}
            <h1 style="font-size:clamp(2rem,3.8vw,2.8rem);font-weight:900;
                       color:#fff;line-height:1.08;letter-spacing:-.02em;
                       margin-bottom:1rem;">
                Lottery<br>Platform
            </h1>

            {{-- Description --}}
            <p style="font-size:13.5px;line-height:1.75;
                      color:rgba(255,255,255,.40);max-width:300px;
                      margin-bottom:1.9rem;">
                Complete lottery management system for your business.
                <span style="color:rgba(155,144,255,.88);font-weight:500;">
                    Track tickets, manage draws,
                </span>
                and
                <span style="color:rgba(155,144,255,.88);font-weight:500;">
                    grow your platform.
                </span>
            </p>

            {{-- Features --}}
            <div style="display:flex;flex-direction:column;gap:13px;">
                @foreach([
                    ['background:rgba(99,102,241,.22);border:1px solid rgba(99,102,241,.28)','📋','Real-time ticket tracking'],
                    ['background:rgba(16,185,129,.20);border:1px solid rgba(16,185,129,.26)','💳','Payment approval workflow'],
                    ['background:rgba(245,158,11,.20);border:1px solid rgba(245,158,11,.26)','📊','Sales & lottery reports'],
                    ['background:rgba(239,68,68,.20);border:1px solid rgba(239,68,68,.26)','⚠️','Expense management'],
                ] as [$s,$ic,$lb])
                <div class="feat">
                    <div class="ficon" style="{{ $s }}">{{ $ic }}</div>
                    <span style="font-size:13.5px;font-weight:500;color:rgba(255,255,255,.62);">
                        {{ $lb }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ══════════════════════════
             RIGHT — login card
             Always visible, centered
             on mobile
        ══════════════════════════ --}}
        <div style="flex:0 0 auto;width:100%;max-width:360px;">
            <div class="card" style="padding:clamp(1.75rem,4vw,2.25rem);">

                {{-- Icon --}}
                <div style="display:flex;justify-content:center;margin-bottom:1.2rem;">
                    <div class="ibox" style="width:52px;height:52px;">
                        <span style="font-size:22px;">🎰</span>
                    </div>
                </div>

                {{-- Heading --}}
                <h2 style="text-align:center;font-size:21px;font-weight:800;
                           color:#fff;letter-spacing:-.01em;margin-bottom:5px;">
                    Welcome Back
                </h2>
                <p style="text-align:center;font-size:12.5px;
                          color:rgba(255,255,255,.35);margin-bottom:1.7rem;">
                    Sign in to
                    <span style="color:#9d93f5;font-weight:600;">Lottery Platform Admin</span>
                </p>

                @if($errors->any())
                <div style="margin-bottom:1rem;display:flex;align-items:flex-start;gap:8px;
                            background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.22);
                            border-radius:10px;padding:11px 13px;font-size:12.5px;color:#fca5a5;">
                    <svg style="width:14px;height:14px;flex-shrink:0;margin-top:1px;"
                         fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf

                    {{-- Email --}}
                    <div style="margin-bottom:13px;">
                        <label style="display:block;font-size:11.5px;font-weight:600;
                                      color:rgba(255,255,255,.48);margin-bottom:7px;">
                            Email Address
                        </label>
                        <div class="field">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="admin@example.com"
                                   required autofocus autocomplete="email">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div style="margin-bottom:13px;" x-data="{ show:false }">
                        <label style="display:block;font-size:11.5px;font-weight:600;
                                      color:rgba(255,255,255,.48);margin-bottom:7px;">
                            Password
                        </label>
                        <div class="field">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input :type="show?'text':'password'"
                                   name="password" placeholder="••••••••" required
                                   autocomplete="current-password">
                            <button type="button" @click="show=!show"
                                    style="background:none;border:none;cursor:pointer;
                                           padding:0;line-height:0;flex-shrink:0;
                                           color:rgba(255,255,255,.28);">
                                <svg x-show="!show" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" x-cloak width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <div style="display:flex;align-items:center;gap:9px;margin-bottom:1.5rem;">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember"
                               style="font-size:13px;font-weight:500;cursor:pointer;
                                      user-select:none;color:rgba(255,255,255,.40);">
                            Remember me
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Sign In
                    </button>
                </form>

                <p style="text-align:center;font-size:11px;
                          color:rgba(255,255,255,.18);margin-top:1.4rem;">
                    © {{ date('Y') }} Lottery Platform Admin. All rights reserved.
                </p>
            </div>
        </div>

    </div>
</div>

{{-- ── Responsive: show left panel only on md+ ─── --}}
<style>
    @media (min-width: 768px) {
        .left-panel { display: block !important; }
    }
</style>

</body>
</html>
