export type AppLoaderQuote = {
    text: string;
    author: string;
    source: string;
    category: 'morning' | 'sunset' | 'lucid' | 'humor';
    mono?: boolean;
};

export const loaderQuotes: AppLoaderQuote[] = [
    {
        text: "J'ai embrassé l'aube d'été.",
        author: 'Rimbaud',
        source: 'Aube, Illuminations',
        category: 'morning',
    },
    {
        text: "Avec l'amour maternel, la vie vous fait à l'aube une promesse qu'elle ne tient jamais.",
        author: 'Romain Gary',
        source: "La Promesse de l'aube",
        category: 'morning',
    },
    {
        text: "La nuit n'est jamais complète. Il y a toujours, au bout du chagrin, une fenêtre ouverte.",
        author: 'Paul Éluard',
        source: "Derniers poèmes d'amour",
        category: 'morning',
    },
    {
        text: 'La terre est bleue comme une orange.',
        author: 'Paul Éluard',
        source: "L'Amour la poésie",
        category: 'morning',
    },
    {
        text: 'To be gorgeous, you must first be seen.',
        author: 'Ocean Vuong',
        source: "On Earth We're Briefly Gorgeous",
        category: 'morning',
    },
    {
        text: 'Sois sage, ô ma douleur, et tiens-toi plus tranquille.',
        author: 'Baudelaire',
        source: 'Recueillement',
        category: 'sunset',
    },
    {
        text: "Là, tout n'est qu'ordre et beauté, luxe, calme et volupté.",
        author: 'Baudelaire',
        source: "L'Invitation au voyage",
        category: 'sunset',
    },
    {
        text: 'Too much joy, I swear, is lost in our desperation to keep it.',
        author: 'Ocean Vuong',
        source: "On Earth We're Briefly Gorgeous",
        category: 'sunset',
    },
    {
        text: "Il nous faut peu de mots pour exprimer l'essentiel.",
        author: 'Paul Éluard',
        source: 'Donner à voir',
        category: 'sunset',
    },
    {
        text: 'On transforme sa main en la mettant dans une autre.',
        author: 'Paul Éluard',
        source: "Derniers poèmes d'amour",
        category: 'sunset',
    },
    {
        text: '& remember, loneliness is still time spent with the world.',
        author: 'Ocean Vuong',
        source: 'Night Sky with Exit Wounds',
        category: 'sunset',
    },
    {
        text: 'Je est un autre.',
        author: 'Rimbaud',
        source: 'Lettre du voyant, mai 1871',
        category: 'lucid',
    },
    {
        text: "Il n'y a pas de hasard, il n'y a que des rendez-vous.",
        author: 'Paul Éluard',
        source: 'Donner à voir',
        category: 'lucid',
    },
    {
        text: 'Il est moins grave de perdre que de se perdre.',
        author: 'Romain Gary',
        source: 'Chien blanc',
        category: 'lucid',
    },
    {
        text: 'Il faut être toujours ivre. De vin, de poésie ou de vertu, à votre guise.',
        author: 'Baudelaire',
        source: 'Enivrez-vous',
        category: 'lucid',
    },
    {
        text: 'Plonger au fond du gouffre pour trouver du nouveau.',
        author: 'Baudelaire',
        source: 'Le Voyage',
        category: 'lucid',
    },
    {
        text: "Vous êtes de moins en moins réels\non se parle on s'écoute\non entend notre propre voix",
        author: 'Laura Vazquez',
        source: '',
        category: 'lucid',
    },
    {
        text: "Plus on observe les éléments du monde, plus on s'aperçoit que leur cohérence vient du fait qu'on les observe.",
        author: 'Laura Vazquez',
        source: 'Les Forces',
        category: 'morning',
    },
    {
        text: 'L’inconnaissance nous recouvre.',
        author: 'Laura Vazquez',
        source: 'Le Livre du large et du long',
        category: 'sunset',
    },
    {
        text: "On n'est pas sérieux quand on a dix-sept ans.",
        author: 'Rimbaud',
        source: 'Roman',
        category: 'humor',
        mono: true,
    },
    {
        text: "I'm a novel far too long. I'm a sentimental song. I am Europe.",
        author: 'Chilly Gonzales',
        source: 'I Am Europe',
        category: 'humor',
        mono: true,
    },
    {
        text: "L'humour est une déclaration de dignité face à ce qui nous arrive.",
        author: 'Romain Gary',
        source: "La Promesse de l'aube",
        category: 'humor',
        mono: true,
    },
    {
        text: 'Does anyone think global warming is a good thing? I love Lady Gaga.',
        author: 'Britney Spears',
        source: 'Twitter',
        category: 'humor',
        mono: true,
    },
    {
        text: 'Information is power.',
        author: 'Aaron Swartz',
        source: "The Internet's Own Boy",
        category: 'lucid',
    },
    {
        text: 'An artist’s duty is to reflect the times.',
        author: 'Nina Simone',
        source: 'Interview',
        category: 'lucid',
    },
    {
        text: "I'll tell you what freedom is: no fear.",
        author: 'Nina Simone',
        source: 'Interview',
        category: 'lucid',
    },
    {
        text: "Ne pas avoir d'opinion, c'est ne pas avoir de limites.",
        author: 'Laura Vazquez',
        source: 'Les Forces',
        category: 'lucid',
    },
    {
        text: 'Personne ne sait écrire, les poèmes existent.',
        author: 'Laura Vazquez',
        source: 'Les Forces',
        category: 'lucid',
    },
];
