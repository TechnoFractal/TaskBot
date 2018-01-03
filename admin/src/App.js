import React from 'react';
import { 
	simpleRestClient, 
	fetchUtils, 
	Admin, 
	Resource 
} from 'admin-on-rest';
import { Delete } from 'admin-on-rest';
import { PostList, PostEdit, PostCreate } from './posts';
import authClient from './authClient';
import PostIcon from 'material-ui/svg-icons/action/book';
import Dashboard from './Dashboard';

const httpClient = (url, options = {}) => {
    if (!options.headers) {
        options.headers = new Headers({ Accept: 'application/json' });
    }
	
    const token = localStorage.getItem('token');
    options.headers.set('token', `${token}`);
    
	return fetchUtils.fetchJson(url, options);
}

const restClient = simpleRestClient('/api', httpClient);

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

export default App;
