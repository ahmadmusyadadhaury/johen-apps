process.env.RAYON_NUM_THREADS ||= '1';

const { build } = await import('vite');

await build();
