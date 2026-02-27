<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Anisa Hub') - {{ config('app.name') }}</title>
    
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --sidebar-bg: #0f172a;
            --surface-bg: #f8fafc;
            --accent-primary: #6366f1;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--surface-bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.05) 0, transparent 50%);
            color: var(--text-main);
            overflow: hidden;
            min-height: 100vh;
        }

        /* Premium Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Sidebar Glassmorphism */
        .sidebar {
            background-color: var(--sidebar-bg);
            color: white;
            min-height: 100vh;
            width: 280px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            position: relative;
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
        }

        .sidebar .brand {
            padding: 2rem 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.85rem 1.5rem;
            margin: 0.2rem 1rem;
            border-radius: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link i {
            font-size: 1.25rem;
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar .nav-link.active {
            color: white;
            background: var(--primary-gradient);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .sidebar .nav-link.active i {
            transform: scale(1.1);
        }

        /* Top Navbar Premium */
        .navbar-top {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .user-profile {
            background: white;
            padding: 6px 16px;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #f1f5f9;
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
        }

        /* Main Content Spacing */
        .main-content {
            padding: 2.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .card {
            border: 1px solid var(--glass-border);
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        /* Custom UI Elements */
        .btn-premium {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(99,102,241,0.25);
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99,102,241,0.35);
            color: white;
        }

        .badge-premium {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Layout Container */
        .app-wrapper {
            display: flex;
            height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Calculator Widget */
        .calculator-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
            cursor: pointer;
            z-index: 1050;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        .calculator-fab:hover { transform: scale(1.1) rotate(15deg); }
        .calculator-fab:active { transform: scale(0.95); }

        .calculator-popup {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 320px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            z-index: 1050;
            display: none;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px) scale(0.9);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .calculator-popup.show {
            display: block;
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .calc-display {
            background: #f1f5f9;
            padding: 20px;
            text-align: right;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .calc-display input {
            width: 100%;
            border: none;
            background: transparent;
            font-size: 2rem;
            text-align: right;
            color: #1e293b;
            font-family: 'Outfit', monospace;
            font-weight: 600;
            outline: none;
        }

        .calc-keys {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            padding: 20px;
        }

        .calc-btn {
            border: none;
            background: white;
            padding: 15px;
            border-radius: 12px;
            font-size: 1.25rem;
            font-weight: 500;
            color: #334155;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }

        .calc-btn:hover { background: #f8fafc; transform: translateY(-2px); }
        .calc-btn:active { transform: translateY(0); }

        .calc-btn.operator {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            font-weight: 700;
        }

        .calc-btn.equal {
            background: var(--primary-gradient);
            color: white;
            grid-column: span 2;
        }

        .calc-btn.clear {
            background: #fee2e2;
            color: #ef4444;
        }
        
        .calc-header {
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
            background: rgba(255,255,255,0.5);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="brand">
                <i class="bi bi-rocket-takeoff-fill"></i>
                <span>Anisa Hub</span>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sales.create') ? 'active' : '' }}" href="{{ route('sales.create') }}">
                        <i class="bi bi-lightning-charge-fill"></i> Point of Sale
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sales.index') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                        <i class="bi bi-stack"></i> Sales History
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}" href="{{ route('loans.index') }}">
                        <i class="bi bi-credit-card"></i> Loans
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('trade-ins.*') ? 'active' : '' }}" href="{{ route('trade-ins.index') }}">
                        <i class="bi bi-arrow-left-right"></i> Trade-Ins
                    </a>
                </li>


                @if(!auth()->user()->isCashier())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="bi bi-box-seam-fill"></i> Inventory
                    </a>
                </li>
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->isManager() || auth()->user()->isOwner())
                <div class="px-4 py-2 small text-uppercase text-muted fw-bold mt-3" style="font-size: 10px; letter-spacing: 1px;">Management</div>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                        <i class="bi bi-piggy-bank-fill"></i> Finance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('suppliers.*') || request()->routeIs('purchase-orders.*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}">
                        <i class="bi bi-truck"></i> Suppliers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.sales') }}">
                        <i class="bi bi-bar-chart-fill"></i> Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="bi bi-people-fill"></i> Team
                    </a>
                </li>
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->isOwner())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" href="{{ route('activity-logs.index') }}">
                        <i class="bi bi-shield-lock-fill"></i> Audit Trail
                    </a>
                </li>
                @endif

                @if(auth()->user()->isOwner())
                <div class="px-4 py-2 small text-uppercase text-muted fw-bold mt-3" style="font-size: 10px; letter-spacing: 1px;">Owner</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('system.*') ? 'active' : '' }}" href="{{ route('system.status') }}">
                        <i class="bi bi-gear-fill"></i> System Management
                    </a>
                </li>
                @endif

                <div class="mt-auto mb-4">
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link w-100 border-0 bg-transparent text-danger">
                                <i class="bi bi-box-arrow-right"></i> Sign Out
                            </button>
                        </form>
                    </li>
                </div>
            </ul>
        </nav>

        <!-- Main Workspace -->
        <div class="content-wrapper">
            <!-- Navbar -->
            <nav class="navbar-top">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">@yield('page-title', 'Overview')</h4>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-profile">
                            <div class="avatar-circle">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-bold small">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 10px;">{{ auth()->user()->roles->first()?->name ?? 'Staff' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Scrollable Content -->
            <div class="main-content">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                            <strong>Quick Alert!</strong>
                        </div>
                        <ul class="mb-0 mt-2 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Floating Calculator Widget -->
    <div class="calculator-fab" onclick="toggleCalculator()">
        <i class="bi bi-calculator"></i>
    </div>

    <div class="calculator-popup" id="calculatorPopup">
        <div class="calc-header">
            <small class="fw-bold text-muted text-uppercase" style="letter-spacing: 1px;">Calculator</small>
            <button class="btn btn-sm btn-light border-0 text-muted" onclick="toggleCalculator()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="calc-display">
            <input type="text" id="calcInput" readonly value="0">
        </div>
        <div class="calc-keys">
            <button class="calc-btn clear" onclick="calcClear()">AC</button>
            <button class="calc-btn operator" onclick="calcAppend('/')">÷</button>
            <button class="calc-btn operator" onclick="calcAppend('*')">×</button>
            <button class="calc-btn" onclick="calcAppend('back')"><i class="bi bi-backspace"></i></button>
            
            <button class="calc-btn" onclick="calcAppend('7')">7</button>
            <button class="calc-btn" onclick="calcAppend('8')">8</button>
            <button class="calc-btn" onclick="calcAppend('9')">9</button>
            <button class="calc-btn operator" onclick="calcAppend('-')">-</button>
            
            <button class="calc-btn" onclick="calcAppend('4')">4</button>
            <button class="calc-btn" onclick="calcAppend('5')">5</button>
            <button class="calc-btn" onclick="calcAppend('6')">6</button>
            <button class="calc-btn operator" onclick="calcAppend('+')">+</button>
            
            <button class="calc-btn" onclick="calcAppend('1')">1</button>
            <button class="calc-btn" onclick="calcAppend('2')">2</button>
            <button class="calc-btn" onclick="calcAppend('3')">3</button>
            
            <button class="calc-btn" onclick="calcAppend('0')">0</button>
            <button class="calc-btn" onclick="calcAppend('.')">.</button>
            <button class="calc-btn equal" onclick="calcCalculate()">=</button>
        </div>
    </div>

    <script>
        const calcPopup = document.getElementById('calculatorPopup');
        const calcInput = document.getElementById('calcInput');
        let calcExpression = '';
        let isResult = false;

        function toggleCalculator() {
            if (calcPopup.style.display === 'block') {
                calcPopup.classList.remove('show');
                setTimeout(() => calcPopup.style.display = 'none', 300);
            } else {
                calcPopup.style.display = 'block';
                // Small delay to allow display:block to apply before transition
                setTimeout(() => calcPopup.classList.add('show'), 10);
            }
        }

        function calcAppend(val) {
            if (isResult && !['+', '-', '*', '/'].includes(val) && val !== 'back') {
                calcExpression = '';
                isResult = false;
            }
            if (isResult && ['+', '-', '*', '/'].includes(val)) {
                isResult = false;
            }

            if (val === 'back') {
                calcExpression = calcExpression.slice(0, -1);
            } else {
                calcExpression += val;
            }
            
            updateDisplay();
        }

        function calcClear() {
            calcExpression = '';
            updateDisplay();
        }

        function calcCalculate() {
            try {
                // Safer evaluation: only allow numbers and operators
                if (!/^[0-9+\-*/. ]+$/.test(calcExpression)) {
                   throw new Error("Invalid Input");
                }
                const result = Function('"use strict";return (' + calcExpression + ')')();
                calcExpression = String(Math.round(result * 10000) / 10000); // Round to avoid float errors
                isResult = true;
                updateDisplay();
            } catch (e) {
                calcInput.value = 'Error';
                setTimeout(() => updateDisplay(), 1000);
            }
        }

        function updateDisplay() {
            calcInput.value = calcExpression || '0';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
