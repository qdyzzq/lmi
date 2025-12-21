<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Document</title>
</head>
<body>
    <div class="w-full min-h-screen bg-gray-50 text-gray-900 font-sans">
    <nav class="w-full fixed top-0 left-0 bg-white shadow-md z-50">
    <div class="w-full px-6 py-4 flex justify-between items-center">
        <h1 class= "text-2xl font-bold">MyPortfolio</h1>
        <div class="space-x-6 hidden md:flex text-lg">
            <a href="#home" class="hover:text-blue-500">Home</a>
            <a href="#about" class="hover:text-blue-500">About</a>
            <a href="#projects" class="hover:text-blue-500">Projects</a>
            <a href="#contact" class="hover:text-blue-500">Contact</a> 
    </div>
    </div>
    </nav>
    {{-- Hero section --}}
    <section id="home" class="min-h-screen flex flex-col items-center justify-center text-center px-6">
        <img src="{{asset('images/portfolio.png')}}"
        alt="Profile Image"
        class="w-80 h-80 rounded-full object-cover mb-6 shadow-lg"
        >
        <h2 class="text-5xl font-bold mb-4"> Hi, I'm Jomari</h2>
        <p class="text-xl text-gray-600 ">A Fullstack Developer</p>
    </section>

    <section id="about" class=" py-20 bg-white">
        <div class="w-full px-6 grid md:grid-cols-2 gap-12 items-center">
            <img src="" alt="about image" class="rounded-2xl shadow-lg"/>
            <div>
                <h3 class=" text-3xl font-bold mb-4">About Me</h3>
                <p class="text-lg text-gray-700 leading-relaxed">
                I'm learning modern web development with TailwindCSS, TypeScript, and React.
                I enjoy building responsive interfaces and improving user experience.
                I also have hands-on QA experience, ensuring that features work seamlessly.
            </p>    
            </div>
        </div>
    </section>
        {{-- project section --}}
        <section id="projects" class="py-20 bg-gray-100">
            <div class="w-full mx-auto px-6">
                <h3 class="text-3xl font-bold text-center mb-10">Projects</h3>
                <div class="grid md:grid-cols-3 gap-10">
                   @foreach ([1,2,3] as $project) 
                        <div key={project} class="bg-white rounded-2xl shadow p-6 hover:shadow-xl transition">
                        <div class="h-40 bg-gray-200 rounded-xl mb-4"></div>
                        <h4 class="text-xl font-semibold mb-2">Project {{$project}}</h4>
                        <p class="text-gray-700"> A saddescription about the project goes here</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        
        <section id="contact" class="py-20 bg-white text-center">
            <h3 class="text-3xl font-bold mb-4">Get In Touch</h3>
            <p class="text-lg text-gray-700 mb-6">Feel free to reach out for collaborations or questions!</p>
            <button class="px-6 py-3 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 transition">Send Messasge</button>
        </section>

        {{-- footer --}}
        <footer class="py-6 text-center bg-gray-200 text-gray-700 mt-10"> 
            © 2025 Jomari — All rights reserved.
        </footer>

    </div>

</body>
</html>