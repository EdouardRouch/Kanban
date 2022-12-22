import { defineStore } from "pinia";
import API_URL from "@/apiUrl";

export const useKanbanStore = defineStore("kanban", {
    state: () => ({
        kanban: {
            id: 0,
            title: '',
            public: false,
            creation_date: '',
            owner: '',
        },
        columns: [
            {
                id: 0,
                title: '',
                kanban_id: 0,
                tasks: [],
            }
        ],
        tasks: ([] as any),
        authorized_users: ([] as Array<String>),
        unauthorized_users: ([] as Array<String>),
    }),

    actions: {
        async getKanban(kanban_id: string) {
            const res = await fetch(API_URL + "/api/kanban/get.php?id=" + kanban_id, { credentials: 'include' });

            if (!res.ok) { throw await res.json(); }

            const json = await res.json();
            this.kanban = json.records.kanban;
            this.columns = json.records.columns;
            this.columns.forEach((column: any) => {
                this.tasks[column.id] = column.tasks;
            });
            this.authorized_users = json.records.authorized_users;
        },

        async deleteTask(task: any) {
            const res = await fetch(API_URL + '/api/task/delete.php', {
                method: "POST",
                headers: {
                    'Content-Type': "application/json; charset=UTF-8",
                },
                credentials: 'include',
                body: JSON.stringify(task),
            });

            if (!res.ok) { throw await res.json() }

            let arr = this.tasks[task.column_id];
            for (let i = 0; i < arr.length; i++) {
                if (arr[i].id === task.id) {
                    arr.splice(i, 1);
                }
            }
            return await res.json();
        },

        async put(type: string, data: any) {
            return await fetch(API_URL + '/api/' + type + '/put.php', {
                method: "POST",
                headers: {
                    'Content-Type': "application/json; charset=UTF-8",
                },
                credentials: 'include',
                body: JSON.stringify(data),
            });
        },

        async post(type: string, data: any) {
            return await fetch(API_URL + '/api/' + type + '/post.php', {
                method: "POST",
                headers: {
                    'Content-Type': "application/json; charset=UTF-8",
                },
                credentials: 'include',
                body: JSON.stringify(data),
            });
        },

        async putTask(task: any) {
            const res = await this.put('task', task);
            if (!res.ok) { throw await res.json() }

            let arr = this.tasks[task.column_id];
            for (let i = 0; i < arr.length; i++) {
                if (arr[i].id === task.id) {
                    arr[i] = task;
                }
            }
            return await res.json();
        },

        async postTask(task: any) {
            const res = await this.post('task', task);
            if (!res.ok) { throw await res.json() }

            const json = await res.json();
            this.tasks[task.column_id].push(json.records.task);

            return json;
        },

        async putKanban() {
            const data = {
                id: this.kanban.id,
                title: this.kanban.title,
                public: this.kanban.public,
                owner: this.kanban.owner,
                authorized_users: this.authorized_users,
                unauthorized_users: this.unauthorized_users,
            }
            const res = await this.put('kanban', data);
            if (!res.ok) {
                this.getKanban(this.kanban.id.toString());
                throw await res.json();
            }

            this.unauthorized_users = [];
            return await res.json();
        },

        canAccess(user: string) {
            return (this.kanban.owner === user) ||
                (this.authorized_users.includes(user));
        },
    },
});
