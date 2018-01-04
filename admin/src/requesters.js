import React from 'react';
import {
	List, 
	Datagrid, 
	ReferenceField, 
	TextField,
	DateField,
	BooleanField
} from 'admin-on-rest';

export const RequesterList = (props) => (
    <List {...props}>
		<Datagrid>
			<TextField source="tele_id" />
			<BooleanField source="is_bot" />
			<TextField source="tele_id" />
			<TextField source="first_name" />
			<TextField source="last_name" />
			<TextField source="user_name" />
			<DateField source="created" />	
			<ReferenceField 
				label="Category" 
				source="categoryId" 
				reference="categories">
				<TextField source="title" />
			</ReferenceField>
			<ReferenceField 
				label="Post" 
				source="postId" 
				reference="posts">
				<TextField source="title" />
			</ReferenceField>
		</Datagrid>
    </List>
);