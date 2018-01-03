import React from 'react';
import { 
	simpleRestClient, 
	fetchUtils, 
	Admin, 
	Resource,
	Delete
} from 'admin-on-rest';
import PostIcon from 'material-ui/svg-icons/action/book';
import CategoryIcon from 'material-ui/svg-icons/action/assessment';
import Dashboard from './Dashboard';
import authClient from './authClient';
import { CategoryList, CategoryEdit } from './categories';
import { PostList, PostEdit, PostCreate } from './posts';

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
		<Resource
			icon={CategoryIcon}
			name="categories" 
			list={CategoryList}
			edit={CategoryEdit} />
	</Admin>
);

export default App;
