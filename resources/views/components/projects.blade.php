<section id="projects">
    <h2 class="text-4xl md:text-5xl mb-12 text-center animate-on-scroll">Featured Projects</h2>
    
    <div class="grid">
        <div class="card animate-on-scroll">
            <img src="{{ asset('images/project1.png') }}" alt="Project 1">
            <div class="card-content">
                <h3 class="text-2xl mb-2">NexGen Dashboard</h3>
                <p class="opacity-70 mb-4">A futuristic analytics platform for blockchain data visualization.</p>
                <div class="flex gap-2">
                    <span class="px-2 py-1 bg-[var(--border-color)] rounded text-sm">Laravel</span>
                    <span class="px-2 py-1 bg-[var(--border-color)] rounded text-sm">Vue.js</span>
                </div>
            </div>
        </div>
        
        <div class="card animate-on-scroll">
            <img src="{{ asset('images/project2.png') }}" alt="Project 2">
            <div class="card-content">
                <h3 class="text-2xl mb-2">CyberFlow App</h3>
                <p class="opacity-70 mb-4">High-performance mobile application for decentralized task management.</p>
                <div class="flex gap-2">
                    <span class="px-2 py-1 bg-[var(--border-color)] rounded text-sm">React Native</span>
                    <span class="px-2 py-1 bg-[var(--border-color)] rounded text-sm">Node.js</span>
                </div>
            </div>
        </div>
        
        <div class="card animate-on-scroll">
            <div class="project-placeholder bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white text-3xl font-bold">
                AI Agent
            </div>
            <div class="card-content">
                <h3 class="text-2xl mb-2">OmniAI Agent</h3>
                <p class="opacity-70 mb-4">Self-learning AI assistant for automated customer support workflows.</p>
                <div class="flex gap-2">
                    <span class="px-2 py-1 bg-[var(--border-color)] rounded text-sm">Python</span>
                    <span class="px-2 py-1 bg-[var(--border-color)] rounded text-sm">OpenAI</span>
                </div>
            </div>
        </div>
    </div>
</section>