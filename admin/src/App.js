import React from 'react';
import { 
	jsonServerRestClient, 
	fetchUtils, 
	Admin, 
	Resource 
} from 'admin-on-rest';
import { Delete } from 'admin-on-rest';
import { PostList, PostEdit, PostCreate } from './posts';
import { UserList } from './users';
import authClient from './authClient';
import PostIcon from 'material-ui/svg-icons/action/book';
import UserIcon from 'material-ui/svg-icons/social/group';
import Dashboard from './Dashboard';

const httpClient = (url, options = {}) => {
    if (!options.headers) {
        options.headers = new Headers({ Accept: 'application/json' });
    }
	
    const token = localStorage.getItem('token');
    options.headers.set('token', `${token}`);
    
	return fetchUtils.fetchJson(url, options);
}

const restClient = jsonServerRestClient('/api', httpClient);

const App = () => (
	<Admin 
		authClient={authClient}
		restClient={restClient}		
		dashboard={Dashboard}>
		<Resource 
			icon={PostIcon}
			name="posts" 
			list={PostList} 
			edit={PostEdit} 
			create={PostCreate}
			remove={Delete} />
	</Admin>
);

//<Resource 
//			icon={UserIcon}
//			name="users" 
//			list={UserList} />

export default App;
