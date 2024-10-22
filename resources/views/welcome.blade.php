@extends('layouts.frontend')
@section('content')
    <!-- Banner Section -->
    <header class="text-center">
        <div class="container">
            <h1 class="poppins-extrabold"><span style="color:#ff5a0b;">15</span>Daily</h1>
            <p>Turn Your Input Into Actionable Tasks Instantly</p>
            <i class="fas fa-microphone fa-lg text-center" style="font-size:2em;"></i>
        </div>
    </header>

    <!-- Intro Section -->
    <section class="intro text-center py-5">
        <div class="container">
            <p class="lead">15Daily helps you record your thoughts, ideas, and inputs, then seamlessly converts them into actionable tasks. Streamline your productivity and keep track of your goals with ease.</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features py-5" style="background:#B3BFBD;">
        <div class="container">
            <h3 class="poppins-bold text-center py-5">Why Choose 15Daily?</h3>
            <div class="row">
                <div class="col-md-4 feature-item text-center">
                    <h4>Record with Ease</h4>
                    <p>Simply record your voice or input text. 15Daily automatically captures your ideas in real-time, making note-taking easier than ever.</p>
                </div>
                <div class="col-md-4 feature-item text-center">
                    <h4>Convert to Tasks</h4>
                    <p>Watch as your recordings are intelligently transformed into structured, actionable tasks, ready to be tackled.</p>
                </div>
                <div class="col-md-4 feature-item text-center">
                    <h4>Stay Organized</h4>
                    <p>Track your progress and stay on top of your responsibilities. 15Daily keeps your life organized and stress-free.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sign Up Section -->
    <section class="signup text-center py-5">
        <div class="container">
            <h2>Get Started Today</h2>
            <p>Sign up now to unlock the full potential of 15Daily and never lose track of your tasks again!</p>
                <a class="btn btn-success btn-lg" href="/register">Sign Up</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 15Daily. All rights reserved.</p>
        </div>
    </footer>
@endsection
