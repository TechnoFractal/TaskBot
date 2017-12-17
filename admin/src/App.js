import React from 'react';
import { jsonServerRestClient, Admin, Resource } from 'admin-on-rest';
import { Delete } from 'admin-on-rest';
import { PostList, PostEdit, PostCreate } from './posts';
import { UserList } from './users';
import authClient from './authClient';
import PostIcon from 'material-ui/svg-icons/action/book';
import UserIcon from 'material-ui/svg-icons/social/group';
import Dashboard from './Dashboard';

const App = () => (
    <Admin 
		authClient={authClient}
		dashboard={Dashboard}
		restClient={
			jsonServerRestClient('http://jsonplaceholder.typicode.com')
		}>
		<Resource 
			icon={PostIcon}
			name="posts" 
			list={PostList} 
			edit={PostEdit} 
			create={PostCreate}
			remove={Delete} />
		<Resource 
			icon={UserIcon}
			name="users" 
			list={UserList} />
    </Admin>
);

export default App;
