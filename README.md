# ROI Calculator with AI

## Overview
This project is a **Return on Investment (ROI) Calculator** powered by AI, built using **Laravel with the TALL Stack** (Tailwind CSS, Alpine.js, Laravel, Livewire). The system enables users to analyze potential business investments, generate ROI reports, and gain actionable insights based on AI-driven analysis.

## Features
- **AI-powered ROI predictions**: The system estimates return on investment based on user inputs.
- **Speech-to-Text**: Users can input data via voice commands.
- **Multi-language support**: The system automatically adapts to different languages.
- **Dynamic scenario analysis**: Users receive insights into best, worst, and expected outcomes.
- **Intuitive UI**: Built with TALL stack for a seamless user experience.

## Tech Stack
- **Backend**: Laravel
- **Frontend**: Tailwind CSS, Alpine.js, Livewire
- **Database**: MySQL
- **AI Services**: Microsoft Azure OpenAI, Speech-to-Text, and DeepSeek AI

## Demo
Try the live demo: [ROI Calculator](https://roi.wbor.dev/)

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo/roi-calculator.git
   cd roi-calculator
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```

3. Configure the environment variables by renaming `.env.example` to `.env` and setting the following keys:
   ```env
   AZURE_OPENAI_BASE_URL=<your_openai_base_url>
   AZURE_OPENAI_API_KEY=<your_openai_api_key>

   AZURE_SPEECH_TO_TEXT_ENDPOINT=<your_speech_to_text_endpoint>
   
   DEEP_SEEK_BASE_URL=<your_deep_seek_base_url>
   DEEP_SEEK_API_KEY=<your_deep_seek_api_key>
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Run database migrations:
   ```bash
   php artisan migrate --seed
   ```

6. Start the development server:
   ```bash
   php artisan serve
   ```

## Usage
1. Register and log in to the application.
2. Create a new business idea and input details via text or voice.
3. Analyze ROI predictions and explore different scenarios.
4. Adjust inputs and refine your business model.

## Contribution
Feel free to fork the repository, create a branch, and submit a pull request for improvements.

## License
This project is licensed under the MIT License.