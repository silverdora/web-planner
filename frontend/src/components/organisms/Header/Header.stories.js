import Header from './Header.vue'

export default {
  title: 'Organisms/Header',
  component: Header,
  tags: ['autodocs'],
}

export const Default = {
  args: {
    navigationLinks: [
      { name: 'Dashboard', href: '/dashboard' },
      { name: 'Categories', href: '/categories' },
      { name: 'Tasks', href: '/tasks' },
      { name: 'Create Task', href: '/tasks/create' },
    ],
  },
}
